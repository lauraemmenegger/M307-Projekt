<?php

$error = false;
$errorMessages = []; // Sammelt verständliche Fehlermeldungen für den User

// Variablen mit Standardwerten initialisieren
$anrede = "";
$vorname = "";
$nachname = "";
$geburtsdatum = "";
$email = "";
$telefon = "";
$strasse = "";
$plz = "";
$ort = "";
$land = "Schweiz";
$datum = "";
$zeitfenster = "";
$anzahl_personen = 1;
$paket = "";
$bemerkungen = "Keine";
$datenschutz = "";
$mailGesendet = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. VORNAME (required)
    if (isset($_POST['vorname'])) {
        $vorname = trim($_POST['vorname']);
        if (empty($vorname)) {
            $error = true;
            $errorMessages[] = "Vorname darf nicht leer sein.";
        }
    } else {
        $error = true;
    }

    // 2. NACHNAME (required)
    if (isset($_POST['nachname'])) {
        $nachname = trim($_POST['nachname']);
        if (empty($nachname)) {
            $error = true;
            $errorMessages[] = "Nachname darf nicht leer sein.";
        }
    } else {
        $error = true;
    }

    // 3. GEBURTSDATUM (required & Mindestalter 16 Jahre)
    if (isset($_POST['geburtsdatum'])) {
        $geburtsdatum = trim($_POST['geburtsdatum']);
        if (empty($geburtsdatum)) {
            $error = true;
            $errorMessages[] = "Geburtsdatum ist erforderlich.";
        } else {
            // Altersprüfung: Mindestens 16 Jahre alt
            $geburtstag = new DateTime($geburtsdatum);
            $heute = new DateTime();
            $alter = $heute->diff($geburtstag)->y;
            if ($alter < 16) {
                $error = true;
                $errorMessages[] = "Du musst mindestens 16 Jahre alt sein.";
            }
        }
    } else {
        $error = true;
    }

    // 4. E-MAIL (required & E-Mail-Format)
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (empty($email)) {
            $error = true;
            $errorMessages[] = "E-Mail-Adresse darf nicht leer sein.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = true;
            $errorMessages[] = "Bitte gib eine gültige E-Mail-Adresse ein.";
        }
    } else {
        $error = true;
    }

    // 5. TELEFON (required & RegEx-Pattern aus dem HTML)
    if (isset($_POST['telefon'])) {
        $telefon = trim($_POST['telefon']);
        $phonePattern = '/^\+?[0-9]{1,4}[ ]?[0-9]{2}[ ]?[0-9]{3}[ ]?[0-9]{2}[ ]?[0-9]{2}$/';
        if (empty($telefon)) {
            $error = true;
            $errorMessages[] = "Telefonnummer darf nicht leer sein.";
        } elseif (!preg_match($phonePattern, $telefon)) {
            $error = true;
            $errorMessages[] = "Format der Telefonnummer ist ungültig (z.B. +41 79 123 45 67).";
        }
    } else {
        $error = true;
    }

    // 6. PLZ (required & RegEx-Pattern [0-9]{4} aus dem HTML)
    if (isset($_POST['plz'])) {
        $plz = trim($_POST['plz']);
        if (empty($plz)) {
            $error = true;
            $errorMessages[] = "Postleitzahl darf nicht leer sein.";
        } elseif (!preg_match('/^[0-9]{4}$/', $plz)) {
            $error = true;
            $errorMessages[] = "Die Postleitzahl muss exakt aus 4 Ziffern bestehen.";
        }
    } else {
        $error = true;
    }

    // 7. GEWÜNSCHTES DATUM (required)
    if (isset($_POST['datum'])) {
        $datum = trim($_POST['datum']);
        if (empty($datum)) {
            $error = true;
            $errorMessages[] = "Bitte wähle ein gewünschtes Datum aus.";
        }
    } else {
        $error = true;
    }

    // 8. Zeitfenster
    if (isset($_POST['zeitfenster'])) {
        $zeitfenster = trim($_POST['zeitfenster']);
        
        // Hier definieren wir die einzig erlaubten Werte (ohne das ausgebuchte Feld!)
        $erlaubte_zeiten = ['16:00 - 18:30', '19:00 - 21:30'];
    
        if (empty($zeitfenster)) {
            $error = true;
            $errorMessages[] = "Bitte wähle ein Zeitfenster aus.";
        } elseif (!in_array($zeitfenster, $erlaubte_zeiten)) {
            // Wenn jemand versucht, die ausgebuchte oder eine gefälschte Zeit zu senden:
            $error = true;
            $errorMessages[] = "Ungültiges Zeitfenster gewählt oder der Termin ist bereits ausgebucht.";
        }
    } else {
        $error = true;
    }

    // 9. ANZAHL PERSONEN (required, min 1, max 20)
    if (isset($_POST['anzahl_personen'])) {
        $anzahl_personen = intval($_POST['anzahl_personen']);
        if ($anzahl_personen < 1 || $anzahl_personen > 20) {
            $error = true;
            $errorMessages[] = "Die Personenanzahl muss zwischen 1 und 20 liegen.";
        }
    } else {
        $error = true;
    }

    // 10. PAKETAUSWAHL (required)
    if (isset($_POST['paket'])) {
        $paket = trim($_POST['paket']);
        
        // Die exakten Werte aus deinen HTML-Radio-Buttons
        $erlaubte_pakete = ['Cocktail Simple', 'Cocktail & Food', 'Cocktail Premium'];
    
        if (!in_array($paket, $erlaubte_pakete)) {
            $error = true;
            $errorMessages[] = "Das ausgewählte Workshop-Paket existiert nicht.";
        }
    } else {
        $error = true;
        $errorMessages[] = "Bitte wähle ein Workshop-Paket aus.";
    }

    // 11. DATENSCHUTZ CHECKBOX (required)
    if (isset($_POST['datenschutz'])) {
        $datenschutz = $_POST['datenschutz'];
    } else {
        $error = true;
        $errorMessages[] = "Du musst den Datenschutzbestimmungen zustimmen.";
    }

    // Optionale Felder einfach einlesen
    $anrede      = trim($_POST['anrede'] ?? '');
    $strasse     = trim($_POST['strasse'] ?? '');
    $ort         = trim($_POST['ort'] ?? '');
    $land        = trim($_POST['land'] ?? 'Schweiz');
    $bemerkungen = trim($_POST['bemerkungen'] ?? 'Keine');


    // VERARBEITUNG NUR WENN KEIN FEHLER AUFGETRETEN IST
    if ($error === false) {

        $empfaenger = $email;
        $betreff = "Buchungsbestaetigung: Dein Cocktail-Workshop";

        // E-Mail Text zusammenbauen (Umlaute direkt nutzen dank UTF-8 Header)
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
        $nachricht .= "Wir freuen uns riesig auf dich!\nMit freundlichen Grüssen,\nDein Cocktail-Team";

        $header = "From: info@cocktail-workshop.ch\r\n";
        $header .= "Reply-To: info@cocktail-workshop.ch\r\n";
        $header .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $mailGesendet = mail($empfaenger, $betreff, $nachricht, $header);
    }
}
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

    <div class="container" style="text-align: center; max-width: 600px; margin: 0 auto; padding: 20px;">
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error === false && $mailGesendet): ?>
            <h1 style="color: #e38f6b;">✓ Buchung erfolgreich!</h1>
            <p class="subtitle">Vielen Dank, <?php echo htmlspecialchars($vorname); ?>!</p>

            <fieldset style="text-align: left; background: #fff6f0; border: 1px solid #e38f6b; padding: 15px; border-radius: 5px;">
                <legend style="color: #e38f6b; font-weight: bold; padding: 0 10px;">Deine Buchungsübersicht</legend>
                <p>Eine Bestätigungsmail wurde soeben an <strong><?php echo htmlspecialchars($email); ?></strong> gesendet.</p>
                <hr style="border: 0; border-top: 1px solid #ccc; margin: 15px 0;">
                <p><strong>Workshop-Paket:</strong> <?php echo htmlspecialchars($paket); ?></p>
                <p><strong>Datum / Zeit:</strong> <?php echo htmlspecialchars($datum); ?> um <?php echo htmlspecialchars($zeitfenster); ?> Uhr</p>
                <p><strong>Teilnehmer:</strong> <?php echo htmlspecialchars($anzahl_personen); ?> Person(en)</p>
            </fieldset>

        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $error === true): ?>
            <h1 style="color: #e63946;">❌ Eingabefehler!</h1>
            <div style="text-align: left; background: #ffe3e3; border: 1px solid #e63946; padding: 15px; border-radius: 5px; color: #a61c26;">
                <strong>Bitte korrigiere die folgenden Fehler im Formular:</strong>
                <ul style="margin-top: 10px; padding-left: 20px;">
                    <?php foreach ($errorMessages as $msg): ?>
                        <li><?php echo htmlspecialchars($msg); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $error === false && !$mailGesendet): ?>
            <h1 style="color: #e63946;">❌ Hoppla!</h1>
            <p>Deine Daten wurden zwar korrekt empfangen, aber unser Mailserver streikt gerade. Bitte kontaktiere uns direkt unter info@cocktail-workshop.ch.</p>
        
        <?php else: ?>
            <h1>Keine Daten empfangen</h1>
            <p>Bitte fülle zuerst das Anmeldeformular aus.</p>
        <?php endif; ?>

        <p style="margin-top: 30px;"><a href="index.html" style="color: #e38f6b; font-weight: bold; text-decoration: none;">← Zurück zum Formular</a></p>
    </div>

</body>
</html>