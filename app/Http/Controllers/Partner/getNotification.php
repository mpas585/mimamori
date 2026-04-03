    /**
     * 組織の通知設定を取得（JSON）
     */
    public function getNotification()
    {
        $organization = $this->getOrganization();

        return response()->json([
            'notification_email_1'     => $organization->notification_email_1,
            'notification_email_2'     => $organization->notification_email_2,
            'notification_email_3'     => $organization->notification_email_3,
            'notification_enabled'     => (bool) $organization->notification_enabled,
            'notification_sms_1'       => $organization->notification_sms_1,
            'notification_sms_2'       => $organization->notification_sms_2,
            'notification_sms_enabled' => (bool) $organization->notification_sms_enabled,
        ]);
    }
