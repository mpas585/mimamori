/*
 * みまもりデバイス - Step4: SR602 + VL53L0X + 2色LED テスト
 * 
 * 配線:
 *   SR602 + → 3.3V / - → GND / OUT → D0 (GPIO2)
 *   VL53L0X VCC → 3.3V / GND → GND / SDA → D4 (GPIO6) / SCL → D5 (GPIO7)
 *   LED 赤(左足) → D1 (GPIO3) / GND(真ん中) → GND / 緑(右足) → D2 (GPIO4)
 * 
 * 動作:
 *   PIR検知 → 距離測定 → 1m以内 = 緑LED / 1m超 = 赤LED
 *   ※抵抗なしのためPWMで明るさ制限（焼損防止）
 */

#include <Wire.h>
#include <VL53L0X.h>

#define PIR_PIN 2    // D0 = GPIO2
#define LED_RED 3    // D1 = GPIO3
#define LED_GREEN 4  // D2 = GPIO4
#define SDA_PIN 6    // D4 = GPIO6
#define SCL_PIN 7    // D5 = GPIO7
#define THRESHOLD_CM 100

// PWM設定（抵抗なしのため明るさを下げる）
#define PWM_BRIGHTNESS 30  // 0-255、30くらいなら安全

VL53L0X sensor;
int detectionCount = 0;
bool lastState = LOW;

void ledOff() {
  analogWrite(LED_RED, 0);
  analogWrite(LED_GREEN, 0);
}

void ledRed() {
  analogWrite(LED_RED, PWM_BRIGHTNESS);
  analogWrite(LED_GREEN, 0);
}

void ledGreen() {
  analogWrite(LED_RED, 0);
  analogWrite(LED_GREEN, PWM_BRIGHTNESS);
}

void setup() {
  Serial.begin(115200);
  pinMode(PIR_PIN, INPUT);
  pinMode(LED_RED, OUTPUT);
  pinMode(LED_GREEN, OUTPUT);
  ledOff();

  Wire.begin(SDA_PIN, SCL_PIN);

  Serial.println("================================");
  Serial.println("  みまもりトーフ Step4");
  Serial.println("  AM312 + VL53L0X + LED テスト");
  Serial.println("================================");
  Serial.println("");

  // VL53L0X初期化
  Serial.print("VL53L0X初期化中...");
  if (!sensor.init()) {
    Serial.println("失敗！");
    // 赤LED点滅でエラー表示
    while (1) {
      ledRed(); delay(200);
      ledOff(); delay(200);
    }
  }
  Serial.println("OK！");

  sensor.setTimeout(500);
  sensor.startContinuous();

  // 起動成功 → 緑LED3秒点灯
  Serial.println("起動成功！緑LED点灯中...");
  ledGreen();
  delay(3000);
  ledOff();

  Serial.println("");
  Serial.println("ウォームアップ中...");
  Serial.println("手をかざすと緑、遠いと赤に光るよ");
  Serial.println("");
}

void loop() {
  bool currentState = digitalRead(PIR_PIN);

  if (currentState == HIGH && lastState == LOW) {
    detectionCount++;

    int distance_mm = sensor.readRangeContinuousMillimeters();

    if (sensor.timeoutOccurred()) {
      Serial.print("[検知 #");
      Serial.print(detectionCount);
      Serial.println("] タイムアウト");
      ledRed();
    } else {
      int distance_cm = distance_mm / 10;
      Serial.print("[検知 #");
      Serial.print(detectionCount);
      Serial.print("] 距離: ");
      Serial.print(distance_cm);
      Serial.print("cm → ");

      if (distance_cm <= THRESHOLD_CM) {
        Serial.println("👤 人間！→ 緑LED");
        ledGreen();
      } else {
        Serial.println("🐕 ペット → 赤LED");
        ledRed();
      }
    }
  }

  if (currentState == LOW && lastState == HIGH) {
    Serial.println("[解除] → LED消灯");
    Serial.println("");
    ledOff();
  }

  lastState = currentState;
  delay(100);
}
