
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de Fidélité</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            background-color: #f1f1f1;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .badge-card {
            width: 320px;
            height: 460px;
            background: #38598b;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-left: 25%;
            margin-top: 20%;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 5px;
            color: white;
            text-transform: uppercase;
            font-weight: bolder;
            letter-spacing: 1px;
        }

        .client-photo img {
            width: 100px;
            height: 100px;
            margin-top: 30px ;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 3px 10px rgba(44, 62, 80, 0.2);
        }

        .client-name {
            margin-top: 25px;
            font-size: 22px;
            color: white;
            font-weight: bolder;
        }

        .divider {
            height: 2px;
            background: linear-gradient(to right, transparent, white, transparent);
            margin: 15px 0;
        }

        .qr-code img {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 15px;
            background-color: #fff;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .footer {
            font-size: 11px;
            color: white;
            margin-top: 15px;
            font-weight: 300;
        }

        .background-pattern {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 160px;
            background: url("{{ asset('assets/flat-geometric-background_23-2148948420-removebg-preview 1.svg') }}") no-repeat center center;
            background-size: cover;

        }
    </style>
</head>
<body>

<div class="badge-card">
    <h1>Carte De Fidélité</h1>
    <div class="client-photo">
        <img src="{{ $photo ? 'data:image/png;base64,' . $photo : 'https://res.cloudinary.com/dvy0saazc/image/upload/v1725507238/uploads/ytk2cqqcoxvgcqap7lm5.jpg' }}" alt="Photo du client">

    </div>
    @if ($client->user)

        <div class="client-name">{{ $client->user->nom .' '. $client->user->prenom }}</div>

        <div class="divider"></div>
    @endif


    <div class="qr-code">
        <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="QR Code">
    </div>
    <div class="footer">Merci de votre fidélité !</div>
    <div class="background-pattern"></div>

</div>
</body>
</html>
