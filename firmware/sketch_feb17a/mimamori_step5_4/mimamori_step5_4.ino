/*
 * みまもりトーフ - Step5: センサー + SIM7080G Cat-M通信
 * 
 * 配線:
 *   AM312 PIR: OUT → D0 (GPIO2) / VCC → 3.3V / GND → GND
 *   VL53L0X:   SDA → D4 (GPIO6) / SCL → D5 (GPIO7) / VCC → 3.3V / GND → GND
 *   LED:       赤 → D1 (GPIO3) / 緑 → D2 (GPIO4) / GND → GND
 *   SIM7080G:  TX → D9 (GPIO9) / RX → D3 (GPIO5) / 5V → VUSB / GND → GND
 * 
 * 動作:
 *   1. 起動 → SIM7080G初期化 → Cat-Mネットワーク接続
 *   2. PIR検知 → 距離測定 → 1m以内=在宅検知
 *   3. 定期的にサーバーへHTTP POSTで状態送信
 */

#include <Wire.h>
#include <VL53L0X.h>

// === ピン定義 ===
#define PIR_PIN    2   // D0: AM312 PIR
#define LED_RED    3   // D1: 赤LED
#define LED_GREEN  4   // D2: 緑LED
#define SIM_TX     5   // D3: XIAO TX → M5Stamp RX
#define I2C_SDA    6   // D4: VL53L0X SDA
#define I2C_SCL    7   // D5: VL53L0X SCL
#define SIM_RX     9   // D9: M5Stamp TX → XIAO RX

// === 設定 ===
#define THRESHOLD_CM       100      // 在宅判定距離(cm)
#define PWM_BRIGHTNESS     30       // LED明るさ(抵抗なし対策)
#define HEARTBEAT_INTERVAL 3600000  // ハートビート間隔(ms) 1時間
#define DETECTION_COOLDOWN 10000    // 検知送信クールダウン(ms) 10秒
#define APN                "iot.1nce.net"
#define SERVER_URL         "http://care.gud.co.jp"

// === グローバル変数 ===
HardwareSerial SimSerial(1);
VL53L0X tofSensor;

bool lteConnected = false;
bool lastPirState = LOW;
int detectionCount = 0;
unsigned long lastHeartbeat = 0;
unsigned long lastDetectionSent = 0;

// ========================================
// LED制御
// ========================================
void ledOff() { analogWrite(LED_RED, 0); analogWrite(LED_GREEN, 0); }
void ledRed() { analogWrite(LED_RED, PWM_BRIGHTNESS); analogWrite(LED_GREEN, 0); }
void ledGreen() { analogWrite(LED_RED, 0); analogWrite(LED_GREEN, PWM_BRIGHTNESS); }

void ledBlink(int pin, int times, int ms) {
  for (int i = 0; i < times; i++) {
    analogWrite(pin, PWM_BRIGHTNESS);
    delay(ms);
    analogWrite(pin, 0);
    delay(ms);
  }
}

// ========================================
// SIM7080G UART通信
// ========================================
String simResponse(unsigned long timeout) {
  String resp = "";
  unsigned long start = millis();
  while (millis() - start < timeout) {
    while (SimSerial.available()) {
      resp += (char)SimSerial.read();
    }
    if (resp.indexOf("OK") >= 0 || resp.indexOf("ERROR") >= 0) break;
    delay(10);
  }
  return resp;
}

String sendAT(const char* cmd, unsigned long timeout) {
  Serial.print("[AT] ");
  Serial.println(cmd);
  
  // バッファクリア
  while (SimSerial.available()) SimSerial.read();
  
  SimSerial.println(cmd);
  String resp = simResponse(timeout);
  resp.trim();
  if (resp.length() > 0) Serial.println(resp);
  return resp;
}

bool sendATwait(const char* cmd, const char* expect, unsigned long timeout) {
  String resp = sendAT(cmd, timeout);
  return resp.indexOf(expect) >= 0;
}

// ATコマンドをリトライ付きで送信
bool sendATretry(const char* cmd, const char* expect, unsigned long timeout, int retries) {
  for (int i = 0; i < retries; i++) {
    if (sendATwait(cmd, expect, timeout)) return true;
    delay(500);
  }
  return false;
}

// ========================================
// SIM7080G 初期化・接続
// ========================================
bool simInit() {
  Serial.println("\n--- SIM7080G 初期化 ---");
  
  SimSerial.begin(9600, SERIAL_8N1, SIM_RX, SIM_TX);
  delay(3000);
  
  // AT応答待ち（最大30秒リトライ）
  Serial.println("モジュール応答待ち...");
  bool found = false;
  for (int i = 0; i < 30; i++) {
    if (sendATwait("AT", "OK", 1000)) { found = true; break; }
  }
  if (!found) {
    Serial.println("[NG] モジュール応答なし");
    return false;
  }
  Serial.println("[OK] モジュール応答あり");
  
  // 基本設定
  sendAT("ATE0", 1000);            // エコーOFF
  sendAT("AT+CMEE=2", 1000);       // 詳細エラー
  sendAT("AT+CMNB=1", 2000);       // Cat-M専用（NVM保存済みだが念のため）
  
  // SIM確認
  String pinResp = sendAT("AT+CPIN?", 5000);
  if (pinResp.indexOf("READY") < 0) {
    Serial.println("[NG] SIM認識できず");
    return false;
  }
  Serial.println("[OK] SIM READY");
  
  return true;
}

bool lteConnect() {
  Serial.println("\n--- LTE接続 ---");
  
  // APN設定
  char cgdcont[64];
  snprintf(cgdcont, sizeof(cgdcont), "AT+CGDCONT=1,\"IP\",\"%s\"", APN);
  sendAT(cgdcont, 2000);
  
  // ネットワーク登録待ち（最大2分）
  Serial.println("ネットワーク登録待ち...");
  bool registered = false;
  for (int i = 0; i < 60; i++) {
    String resp = sendAT("AT+CEREG?", 2000);
    if (resp.indexOf("0,1") >= 0 || resp.indexOf("0,5") >= 0) { registered = true; break; }
    if (resp.indexOf("0,3") >= 0) {
      Serial.println("[NG] 登録拒否");
      return false;
    }
    delay(2000);
  }
  if (!registered) {
    Serial.println("[NG] 登録タイムアウト");
    return false;
  }
  
  // キャリア確認
  sendAT("AT+COPS?", 3000);
  sendAT("AT+CSQ", 1000);
  
  // データ接続
  char cncfg[64];
  snprintf(cncfg, sizeof(cncfg), "AT+CNCFG=0,1,\"%s\"", APN);
  sendAT(cncfg, 2000);
  
  String actResp = sendAT("AT+CNACT=0,1", 10000);
  if (actResp.indexOf("ACTIVE") < 0) {
    // 既にACTIVEかもしれない
    String chk = sendAT("AT+CNACT?", 3000);
    if (chk.indexOf("0,1,") < 0) {
      Serial.println("[NG] データ接続失敗");
      return false;
    }
  }
  
  Serial.println("[OK] LTE接続完了！");
  lteConnected = true;
  return true;
}

// ========================================
// HTTP送信
// ========================================
bool httpPost(const char* path, const char* jsonBody) {
  if (!lteConnected) return false;
  
  Serial.print("[HTTP] POST → ");
  Serial.println(path);
  
  // 前の接続があれば切断（エラーは無視）
  sendAT("AT+SHDISC", 2000);
  delay(1000);
  
  // HTTP設定
  char urlCmd[128];
  snprintf(urlCmd, sizeof(urlCmd), "AT+SHCONF=\"URL\",\"%s\"", SERVER_URL);
  if (!sendATwait(urlCmd, "OK", 2000)) return false;
  if (!sendATwait("AT+SHCONF=\"BODYLEN\",1024", "OK", 1000)) return false;
  if (!sendATwait("AT+SHCONF=\"HEADERLEN\",350", "OK", 1000)) return false;
  
  // 接続
  if (!sendATwait("AT+SHCONN", "OK", 10000)) {
    Serial.println("[NG] HTTP接続失敗");
    return false;
  }
  
  // ヘッダー設定
  sendAT("AT+SHAHEAD=\"Content-Type\",\"application/json\"", 1000);
  
  // ボディ設定 → ">"プロンプトを待つ
  int bodyLen = strlen(jsonBody);
  char bodCmd[32];
  snprintf(bodCmd, sizeof(bodCmd), "AT+SHBOD=%d,3000", bodyLen);
  
  Serial.print("[AT] ");
  Serial.println(bodCmd);
  while (SimSerial.available()) SimSerial.read();
  SimSerial.println(bodCmd);
  
  // ">"を待つ
  unsigned long start = millis();
  bool gotPrompt = false;
  while (millis() - start < 3000) {
    if (SimSerial.available()) {
      char c = SimSerial.read();
      if (c == '>') { gotPrompt = true; break; }
    }
  }
  if (!gotPrompt) {
    Serial.println("[NG] BODYプロンプト待ちタイムアウト");
    sendAT("AT+SHDISC", 2000);
    return false;
  }
  
  // ボディ送信 → OKを待つ
  SimSerial.print(jsonBody);
  String bodResp = simResponse(3000);
  Serial.println(bodResp);
  if (bodResp.indexOf("OK") < 0) {
    Serial.println("[NG] BODY送信失敗");
    sendAT("AT+SHDISC", 2000);
    return false;
  }
  
  // POSTリクエスト送信
  char reqCmd[128];
  snprintf(reqCmd, sizeof(reqCmd), "AT+SHREQ=\"%s\",3", path);
  sendAT(reqCmd, 3000);  // まずOKを受け取る
  
  // +SHREQ: "POST",200,xx を非同期で待つ（最大30秒）
  String shreqResp = "";
  start = millis();
  while (millis() - start < 30000) {
    while (SimSerial.available()) {
      shreqResp += (char)SimSerial.read();
    }
    if (shreqResp.indexOf("+SHREQ:") >= 0) break;
    delay(100);
  }
  shreqResp.trim();
  Serial.print("[SHREQ] ");
  Serial.println(shreqResp);
  
  bool success = shreqResp.indexOf("200") >= 0 || shreqResp.indexOf("201") >= 0;
  
  if (success) {
    sendAT("AT+SHREAD=0,500", 3000);
    Serial.println("[OK] HTTP送信成功！");
  } else {
    Serial.print("[NG] HTTPステータス: ");
    Serial.println(shreqResp);
  }
  
  sendAT("AT+SHDISC", 2000);
  return success;
}

// ========================================
// データ送信ヘルパー
// ========================================
void sendDetection(int distance_cm, bool isNear) {
  if (millis() - lastDetectionSent < DETECTION_COOLDOWN) return;
  
  char json[200];
  snprintf(json, sizeof(json),
    "{\"type\":\"detection\",\"count\":%d,\"distance\":%d,\"near\":%s,\"rssi\":%d}",
    detectionCount, distance_cm,
    isNear ? "true" : "false",
    getSignalStrength()
  );
  
  httpPost("/test_mock/mimamori/index.php", json);
  lastDetectionSent = millis();
}

void sendHeartbeat() {
  char json[128];
  snprintf(json, sizeof(json),
    "{\"type\":\"heartbeat\",\"uptime\":%lu,\"detections\":%d,\"rssi\":%d}",
    millis() / 1000,
    detectionCount,
    getSignalStrength()
  );
  
  httpPost("/test_mock/mimamori/index.php", json);
  lastHeartbeat = millis();
}

int getSignalStrength() {
  String resp = sendAT("AT+CSQ", 1000);
  int idx = resp.indexOf("+CSQ: ");
  if (idx < 0) return -1;
  return resp.substring(idx + 6, resp.indexOf(",", idx)).toInt();
}

// ========================================
// センサー初期化
// ========================================
bool sensorInit() {
  Serial.println("\n--- センサー初期化 ---");
  
  Wire.begin(I2C_SDA, I2C_SCL);
  
  Serial.print("VL53L0X...");
  if (!tofSensor.init()) {
    Serial.println("失敗！");
    return false;
  }
  tofSensor.setTimeout(500);
  tofSensor.startContinuous();
  Serial.println("OK！");
  
  pinMode(PIR_PIN, INPUT);
  Serial.println("AM312 PIR...OK！");
  
  return true;
}

// ========================================
// メインセットアップ
// ========================================
void setup() {
  Serial.begin(115200);
  delay(1000);
  
  pinMode(LED_RED, OUTPUT);
  pinMode(LED_GREEN, OUTPUT);
  ledOff();
  
  Serial.println("================================");
  Serial.println("  みまもりトーフ Step5");
  Serial.println("  センサー + Cat-M通信");
  Serial.println("================================\n");
  
  // --- センサー初期化 ---
  if (!sensorInit()) {
    Serial.println("[FATAL] センサー初期化失敗");
    while (1) { ledBlink(LED_RED, 3, 200); delay(1000); }
  }
  ledBlink(LED_GREEN, 2, 200);
  
  // --- SIM7080G初期化 ---
  if (!simInit()) {
    Serial.println("[FATAL] SIM7080G初期化失敗");
    while (1) { ledBlink(LED_RED, 5, 200); delay(1000); }
  }
  ledBlink(LED_GREEN, 3, 200);
  
  // --- LTE接続 ---
  if (!lteConnect()) {
    Serial.println("[FATAL] LTE接続失敗");
    while (1) { ledBlink(LED_RED, 7, 200); delay(1000); }
  }
  
  // 接続成功 → 緑LED 3秒点灯
  ledGreen();
  Serial.println("\n================================");
  Serial.println("  みまもりトーフ 起動完了！");
  Serial.println("================================\n");
  delay(3000);
  ledOff();
  
  // 初回ハートビート送信
  sendHeartbeat();
}

// ========================================
// メインループ
// ========================================
void loop() {
  // --- PIR検知処理 ---
  bool pirState = digitalRead(PIR_PIN);
  
  if (pirState == HIGH && lastPirState == LOW) {
    detectionCount++;
    
    int distance_mm = tofSensor.readRangeContinuousMillimeters();
    
    if (tofSensor.timeoutOccurred()) {
      Serial.print("[検知 #");
      Serial.print(detectionCount);
      Serial.println("] ToFタイムアウト");
      ledRed();
    } else {
      int distance_cm = distance_mm / 10;
      bool isNear = distance_cm <= THRESHOLD_CM;
      
      Serial.print("[検知 #");
      Serial.print(detectionCount);
      Serial.print("] 距離: ");
      Serial.print(distance_cm);
      Serial.print("cm → ");
      Serial.println(isNear ? "在宅検知 → 緑" : "遠方 → 赤");
      
      if (isNear) ledGreen(); else ledRed();
      
      // サーバーに送信
      sendDetection(distance_cm, isNear);
    }
  }
  
  if (pirState == LOW && lastPirState == HIGH) {
    Serial.println("[解除] → LED消灯\n");
    ledOff();
  }
  
  lastPirState = pirState;
  
  // --- 定期ハートビート ---
  if (millis() - lastHeartbeat > HEARTBEAT_INTERVAL) {
    sendHeartbeat();
  }
  
  // --- SIMモジュールからの非同期メッセージ処理 ---
  while (SimSerial.available()) {
    String msg = SimSerial.readStringUntil('\n');
    msg.trim();
    if (msg.length() > 0) {
      Serial.print("[SIM] ");
      Serial.println(msg);
      
      // 接続断検知
      if (msg.indexOf("DEACTIVE") >= 0) {
        Serial.println("[WARN] PDP切断検知 → 再接続");
        lteConnected = false;
        lteConnect();
      }
    }
  }
  
  // --- デバッグ用：シリアルモニタからAT手動入力 ---
  while (Serial.available()) {
    String cmd = Serial.readStringUntil('\n');
    cmd.trim();
    if (cmd.length() > 0) sendAT(cmd.c_str(), 3000);
  }
  
  delay(100);
}
