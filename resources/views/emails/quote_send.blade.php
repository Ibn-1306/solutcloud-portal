<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis PREMIUM SOLUTCLOUD</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background-color: #0F172A; padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">SOLUTCLOUD</h1>
                            <p style="color: #0D9488; margin: 5px 0 0; font-size: 14px; font-weight: bold;">Devis PREMIUM — {{ $quote->quote_number }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #0F172A; margin: 0 0 20px; font-size: 20px;">Bonjour {{ $quote->customer_name }},</h2>
                            <p style="color: #64748B; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
                                Suite a votre demande, veuillez trouver ci-dessous votre devis pour l'offre SOLUTCLOUD PREMIUM.
                            </p>
                            <div style="background-color: #f1f5f9; border-radius: 8px; padding: 25px; margin: 25px 0;">
                                <p style="color: #0F172A; font-size: 14px; margin: 0 0 15px; font-weight: bold;">Detail du devis :</p>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 5px 0; color: #64748B; font-size: 13px; width: 150px;">Numero :</td>
                                        <td style="padding: 5px 0; color: #0F172A; font-size: 14px; font-weight: bold;">{{ $quote->quote_number }}</td>
                                    </tr>
                                    @if($quote->company_name)
                                    <tr>
                                        <td style="padding: 5px 0; color: #64748B; font-size: 13px;">Entreprise :</td>
                                        <td style="padding: 5px 0; color: #0F172A; font-size: 14px; font-weight: bold;">{{ $quote->company_name }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="padding: 5px 0; color: #64748B; font-size: 13px;">Montant :</td>
                                        <td style="padding: 5px 0; color: #0F172A; font-size: 14px; font-weight: bold;">{{ number_format($quote->amount, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #64748B; font-size: 13px;">Duree :</td>
                                        <td style="padding: 5px 0; color: #0F172A; font-size: 14px; font-weight: bold;">{{ $quote->duration }} mois</td>
                                    </tr>
                                </table>
                            </div>
                            @if($quote->description)
                            <div style="background-color: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0;">
                                <p style="color: #64748B; font-size: 13px; margin: 0 0 10px; font-weight: bold;">Description :</p>
                                <p style="color: #475569; font-size: 14px; line-height: 1.6; margin: 0;">{{ $quote->description }}</p>
                            </div>
                            @endif
                            <p style="color: #64748B; font-size: 14px; line-height: 1.6; margin: 25px 0 0;">
                                Pour toute question ou assistance, contactez-nous au <strong style="color: #0F172A;">+225 01 01 55 95 05</strong> ou par e-mail a <a href="mailto:sales@i-solutions.ci" style="color: #0D9488; font-weight: bold; text-decoration: none;">sales@i-solutions.ci</a>.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 30px; text-align: center;">
                            <p style="color: #94a3b8; font-size: 12px; margin: 0;">&copy; 2026 I-SOLUTIONS CI — SOLUTCLOUD</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
