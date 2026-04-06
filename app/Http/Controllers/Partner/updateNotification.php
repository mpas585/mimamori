    /**
     * 組織の通知設定を更新
     */
    public function updateNotification(Request $request)
    {
        $organization = $this->getOrganization();

        $request->validate([
            'notification_email_1'    => 'nullable|email|max:255',
            'notification_email_2'    => 'nullable|email|max:255',
            'notification_email_3'    => 'nullable|email|max:255',
            'notification_enabled'    => 'nullable|boolean',
            'notification_sms_1'      => 'nullable|string|max:20',
            'notification_sms_2'      => 'nullable|string|max:20',
            'notification_sms_enabled'=> 'nullable|boolean',
        ]);

        $organization->update([
            'notification_email_1'     => $request->notification_email_1 ?: null,
            'notification_email_2'     => $request->notification_email_2 ?: null,
            'notification_email_3'     => $request->notification_email_3 ?: null,
            'notification_enabled'     => $request->has('notification_enabled') ? (bool) $request->notification_enabled : true,
            'notification_sms_1'       => \App\Helpers\PhoneHelper::normalize($request->notification_sms_1),
            'notification_sms_2'       => \App\Helpers\PhoneHelper::normalize($request->notification_sms_2),
            'notification_sms_enabled' => $request->has('notification_sms_enabled') ? (bool) $request->notification_sms_enabled : false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => '通知設定を更新しました',
            ]);
        }

        return back()->with('success', '通知設定を更新しました');
    }


