<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur notre plateforme</title>
</head>
<body>
<h1>Bienvenue, {{ $user->prenom }} {{ $user->nom }} !</h1>
<p>Merci de vous être inscrit sur notre plateforme. Nous sommes ravis de vous accueillir.</p>
<p>Voici votre login : <strong>{{ $user->login }}</strong></p>
<p>Si vous avez des questions ou des préoccupations, n'hésitez pas à nous contacter.</p>
<p>Cordialement,<br>L'équipe</p>
</body>
</html>
