<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #444; line-height: 1.6; }
        .title { color: #2c7a7b; font-size: 20px; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        td { padding: 10px; border-bottom: 1px solid #f9f9f9; }
        .label { font-weight: bold; width: 150px; color: #718096; }
        .footer { margin-top: 30px; font-size: 12px; color: #a0aec0; font-style: italic; }
    </style>
</head>
<body>
    <div class="title">📦 ARCHIVE : Nouveau client créé</div>
    <p>Une nouvelle instance a été générée depuis SOLUTCLOUD Gestion.</p>

    <table>
        <tr>
            <td class="label">Client :</td>
            <td>{{ $name }}</td>
        </tr>
        <tr>
            <td class="label">Email :</td>
            <td>{{ $email }}</td>
        </tr>
        <tr>
            <td class="label">Mot de passe :</td>
            <td>{{ $password }}</td>
        </tr>
        <tr>
            <td class="label">Instance URL :</td>
            <td>{{ $url }}</td>
        </tr>
        <tr>
            <td class="label">Date :</td>
            <td>{{ $date }}</td>
        </tr>
    </table>

    <div class="footer">
        Ceci est une copie destinée à l'administration I-SOLUTIONS.
    </div>
</body>
</html>