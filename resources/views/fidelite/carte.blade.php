<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de fidélité</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 400px;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            text-align: center;
            padding: 0;
        }
        .card-header {
            background: linear-gradient(45deg, #ff7e5f, #feb47b);
            color: white;
            padding: 20px;
            font-size: 1.5em;
            position: relative;
        }
        .card-header img {
            border-radius: 50%;
            width: 90px;
            height: 90px;
            border: 4px solid white;
            margin-bottom: 10px;
        }
        .info-section {
            padding: 15px 25px;
            margin: 15px 0;
            text-align: left;
        }
        .info-section p {
            margin: 10px 0;
            font-size: 1.1em;
        }
        .info-label {
            font-weight: bold;
            color: #e67e22;
        }
        .qr-code img {
            width: 160px;
            height: 160px;
            margin: 20px auto;
            display: block;
        }
        .footer {
            background-color: #ff7e5f;
            color: white;
            padding: 15px;
            font-size: 0.9em;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .footer img {
            width: 60px;
            height: auto;
            margin-top: 10px;
        }
        /* Card shadow effect */
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 150px;
            background: rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
    </style>
</head>

<body>

<div class="card">
    <div class="card-header">
        <!-- Utiliser une image par défaut si $photo est vide -->
        <img src="{{ $photo ? 'data:image/png;base64,' . $photo : 'https://res.cloudinary.com/dvy0saazc/image/upload/v1725507238/uploads/ytk2cqqcoxvgcqap7lm5.jpg' }}" alt="Profile photo of user" style="width: 100%; height: 100%; object-fit: cover;">
        <h1>Carte de fidélité</h1>
    </div>

    <div class="info-section">
        <p><span class="info-label">Nom :</span> {{ $client->surname }}</p>
        <p><span class="info-label">Téléphone :</span> {{ $client->telephone }}</p>
        <p><span class="info-label">Email :</span> {{ $client->email }}</p>
    </div>

    @if($client->user)
        <div class="info-section">
            <h2>Compte utilisateur</h2>
            <p><span class="info-label">Nom d'utilisateur :</span> {{ $client->user->nom }}</p>
            <p><span class="info-label">Email :</span> {{ $client->user->email }}</p>
            <p><span class="info-label">État du compte :</span> {{ $client->user->etat }}</p>
        </div>
    @endif

    <div class="qr-code">
        <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="QR Code" style="width: 160px; height: 160px; border-radius: 8px;">
    </div>

    <div class="footer">
        <p>Merci de votre fidélité !</p>
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Loyalty_card_generic_icon.svg/1024px-Loyalty_card_generic_icon.svg.png" alt="Logo fidélité">
    </div>
</div>

</body>
</html>
