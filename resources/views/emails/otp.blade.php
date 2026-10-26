<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi OTP — SportPlace</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F4F7F5; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #334155; line-height: 1.6;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F4F7F5; padding: 40px 15px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 520px; background-color: #ffffff; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #06231B; padding: 30px 20px;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: bold; letter-spacing: -0.5px;">
                                Sport<span style="color: #10B981;">Place</span>
                            </h1>
                            <p style="margin: 4px 0 0 0; color: #A7F3D0; font-size: 12px; font-weight: 500;">
                                Platform Sewa Lapangan Olahraga Terpercaya
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 35px 30px;">
                            <h2 style="margin: 0 0 12px 0; font-size: 18px; font-weight: 700; color: #0F172A;">
                                Halo, {{ $user->name }}!
                            </h2>
                            <p style="margin: 0 0 24px 0; font-size: 14px; color: #64748B;">
                                Terima kasih telah mendaftar di SportPlace. Gunakan kode verifikasi (OTP) di bawah ini untuk mengonfirmasi akun email Anda:
                            </p>

                            <!-- OTP Box -->
                            <div style="background-color: #E8F5F1; border: 2px dashed #0B7B69; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px;">
                                <span style="font-family: 'Courier New', Courier, monospace; font-size: 36px; font-weight: 800; color: #0B7B69; letter-spacing: 8px; display: inline-block;">
                                    {{ $otpCode }}
                                </span>
                            </div>

                            <!-- Expiry Info -->
                            <p style="margin: 0 0 16px 0; font-size: 13px; color: #475569; text-align: center;">
                                Kode ini berlaku selama <strong>{{ $ttlMinutes }} menit</strong>.
                            </p>

                            <!-- Warning -->
                            <div style="background-color: #FFFBEB; border-left: 4px solid #F59E0B; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 12px; color: #B45309; line-height: 1.5;">
                                    <strong>Penting:</strong> Jangan berikan kode ini kepada siapa pun, termasuk pihak yang mengatasnamakan SportPlace. Tim kami tidak pernah meminta kode OTP Anda.
                                </p>
                            </div>

                            <p style="margin: 0; font-size: 13px; color: #94A3B8;">
                                Jika Anda tidak merasa melakukan pendaftaran di SportPlace, Anda dapat mengabaikan email ini dengan aman.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #F8FAFC; padding: 20px; border-top: 1px solid #F1F5F9;">
                            <p style="margin: 0; font-size: 11px; color: #94A3B8;">
                                &copy; {{ date('Y') }} SportPlace. Seluruh hak cipta dilindungi.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

