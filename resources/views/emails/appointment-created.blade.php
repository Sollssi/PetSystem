<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de turno</title>
</head>
<body style="margin:0;padding:24px;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">
        <tr>
            <td style="padding:24px 28px;background:#0f172a;color:#ffffff;">
                <p style="margin:0;font-size:12px;letter-spacing:1px;text-transform:uppercase;opacity:.85;">VetClinic Manager</p>
                <h1 style="margin:8px 0 0 0;font-size:22px;line-height:1.3;">Turno confirmado</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:26px 28px 8px 28px;">
                <p style="margin:0 0 14px 0;font-size:15px;line-height:1.6;">Hola <strong>{{ $appointment->user->name }}</strong>, registramos tu turno correctamente.</p>
                <p style="margin:0 0 18px 0;font-size:14px;line-height:1.6;color:#475569;">Este es el resumen de tu solicitud:</p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="padding:10px 12px;background:#f8fafc;font-size:13px;color:#64748b;width:35%;">Mascota</td>
                        <td style="padding:10px 12px;font-size:14px;color:#0f172a;">{{ $appointment->pet->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 12px;background:#f8fafc;font-size:13px;color:#64748b;">Fecha y hora</td>
                        <td style="padding:10px 12px;font-size:14px;color:#0f172a;">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 12px;background:#f8fafc;font-size:13px;color:#64748b;">Tipo</td>
                        <td style="padding:10px 12px;font-size:14px;color:#0f172a;">{{ ucfirst($appointment->type) }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 12px;background:#f8fafc;font-size:13px;color:#64748b;">Estado</td>
                        <td style="padding:10px 12px;font-size:14px;color:#0f172a;">{{ ucfirst($appointment->status) }}</td>
                    </tr>
                    @if($appointment->description)
                        <tr>
                            <td style="padding:10px 12px;background:#f8fafc;font-size:13px;color:#64748b;vertical-align:top;">Descripción</td>
                            <td style="padding:10px 12px;font-size:14px;color:#0f172a;line-height:1.5;">{{ $appointment->description }}</td>
                        </tr>
                    @endif
                </table>

                <p style="margin:18px 0 0 0;font-size:14px;line-height:1.6;color:#475569;">Podés gestionar tus turnos y revisar recordatorios de vacunación desde tu panel de usuario.</p>
            </td>
        </tr>
        <tr>
            <td style="padding:20px 28px 26px 28px;">
                <a href="{{ url('/dashboard') }}" style="display:inline-block;background:#0f172a;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:10px 16px;border-radius:8px;">Ir al panel</a>
            </td>
        </tr>
        <tr>
            <td style="padding:18px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;">
                <p style="margin:0;font-size:12px;color:#64748b;">Equipo VetClinic Manager · Mensaje automático de confirmación</p>
            </td>
        </tr>
    </table>
</body>
</html>
