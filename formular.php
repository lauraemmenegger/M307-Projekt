<?php
// 1. SCHRITT: DATEN SICHER AUSLESEN
// htmlspecialchars verhindert, dass böswilliger Code ausgeführt wird (Sicherheit!)
$anrede          = htmlspecialchars($_POST['anrede'] ?? '');
$vorname         = htmlspecialchars($_POST['vorname'] ?? '');
$nachname        = htmlspecialchars($_POST['nachname'] ?? '');
$geburtsdatum    = htmlspecialchars($_POST['geburtsdatum'] ?? '');
$email           = htmlspecialchars($_POST['email'] ?? '');
$telefon         = htmlspecialchars($_POST['telefon'] ?? '');
$strasse         = htmlspecialchars($_POST['strasse'] ?? '');
$plz             = htmlspecialchars($_POST['plz'] ?? '');
$ort             = htmlspecialchars($_POST['ort'] ?? '');
$land            = htmlspecialchars($_POST['land'] ?? 'Schweiz');
$datum           = htmlspecialchars($_POST['datum'] ?? '');
$zeitfenster     = htmlspecialchars($_POST['zeitfenster'] ?? '');
$anzahl_personen = htmlspecialchars($_POST['anzahl_personen'] ?? '1');
$paket           = htmlspecialchars($_POST['paket'] ?? '');
$bemerkungen     = htmlspecialchars($_POST['bemerkungen'] ?? 'Keine');

// 2. SCHRITT: DIE E-MAIL ERSTELLEN & ABSENDEN
// Wohin geht die Reise? An die eingegebene E-Mail-Adresse des Kunden!
$empfaenger = $email; 
$betreff = "Buchungsbestaetigung: Dein Cocktail-Workshop";

// Hier bauen wir den Text der E-Mail zusammen
$nachricht = "Hallo " . $vorname . " " . $nachname . ",\n\n";
$nachricht .= "Vielen Dank für deine Buchung! Hier ist deine Übersicht:\n\n";
$nachricht .= "--- DEINE DATEN ---\n";
$nachricht .= "Name: " . $anrede . " " . $vorname . " " . $nachname . "\n";
$nachricht .= "Geburtsdatum: " . $geburtsdatum . "\n";
$nachricht .= "Telefon: " . $telefon . "\n";
$nachricht .= "Adresse: " . $strasse . ", " . $plz . " " . $ort . " (" . $land . ")\n\n";
$nachricht .= "--- EVENT DETAILS ---\n";
$nachricht .= "Datum: " . $datum . "\n";
$nachricht .= "Uhrzeit: " . $zeitfenster . " Uhr\n";
$nachricht .= "Personenanzahl: " . $anzahl_personen . "\n";
$nachricht .= "Gewähltes Paket: " . $paket . "\n";
$nachricht .= "Deine Wünsche: " . $bemerkungen . "\n\n";
$nachricht .= "Wir freuen uns riesig auf dich!\nMit freundlichen Gruessen,\nDein Cocktail-Team";

// Wichtige technische Zusatzinfos für den E-Mail-Versand (Absender definieren)
$header = "From: info@cocktail-workshop.ch\r\n";
$header .= "Reply-To: info@cocktail-workshop.ch\r\n";
$header .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Der eigentliche PHP-Mail-Befehl (Schickt die E-Mail ab)
$mailGesendet = mail($empfaenger, $betreff, $nachricht, $header);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buchungsbestätigung</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<!--Css Teil in php -->
<div class="container" style="text-align: center;">
    <?php if ($mailGesendet): ?>
        <h1 style="color: #e38f6b;">✓ Buchung erfolgreich!</h1>
        <p class="subtitle">Vielen Dank, <?php echo $vorname; ?>!</p>
        
        <fieldset style="text-align: left; background: #fff6f0; border-color: #e38f6b;">
            <legend style="color: #e38f6b;">Deine Buchungsübersicht</legend>
            <p>Eine Bestätigungsmail wurde soeben an <strong><?php echo $email; ?></strong> gesendet.</p>
            <hr style="border: 0; border-top: 1px solid #ccc; margin: 15px 0;">
            <p><strong>Workshop-Paket:</strong> <?php echo $paket; ?></p>
            <p><strong>Datum / Zeit:</strong> <?php echo $datum; ?> um <?php echo $zeitfenster; ?> Uhr</p>
            <p><strong>Teilnehmer:</strong> <?php echo $anzahl_personen; ?> Person(en)</p>
        </fieldset>
        
    <?php else: ?>
        <h1 style="color: #e63946;">❌ Hoppla!</h1>
        <p>Deine Daten wurden zwar empfangen, aber die Bestätigungsmail konnte nicht gesendet werden. Bitte kontaktiere uns direkt.</p>
    <?php endif; ?>

    <p style="margin-top: 30px;"><a href="index.html" style="color: var(--primary-color); font-weight: bold; text-decoration: none;">← Zurück zum Formular</a></p>
</div>

</body>
</html>