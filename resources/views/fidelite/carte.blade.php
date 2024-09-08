<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carte de fidélité</title>
</head>
<body style="font-family: 'Roboto', sans-serif; text-align: center; background-color: #f1f1f1; display: flex; justify-content: center; align-items: center; width: 100%; height: 100vh;">
<div style="background-color: #fff; border-radius: 7px; box-shadow: 0 0 50px rgba(0,0,0,0.1); width: 350px; padding: 20px; position: relative; display: flex; flex-direction: column; align-items: center;">
    <span style="color: #404040; font-size: 20px; font-weight: bold; margin-bottom: 20px;">Carte De Fidélité</span>
    <div style="width: 134px; height: 134px; border-radius: 50%; overflow: hidden; margin-bottom: 20px;">
        <img src="data:image/png;base64,{{ $photo }}" alt="Profile photo of user" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <span style="color: #404040; font-size: 20px; font-weight: bold;">{{ $client->surname }} {{ $client->user ? $client->user->nom : '' }}</span>
    <div style="width: 160px; height: 160px; border-radius: 8px; background-color: white; box-shadow: 0 0 25px rgba(0,0,0,0.1); display: flex; justify-content: center; align-items: center; margin-top: 20px;">
        <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="QR Code" style="width: 160px; height: 160px; border-radius: 8px;">
    </div>
</div>
</body>
</html>
