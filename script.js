/**
 * DYNAMISCHE DATUMS-BEGRENZUNGEN FÜR DAS FORMULAR
 */
document.addEventListener("DOMContentLoaded", function() {
    
    // Heutiges Datum ermitteln
    const heute = new Date();
    
    // Datum formatieren zu YYYY-MM-DD (wichtig für HTML5-Date-Inputs)
    const jahr = heute.getFullYear();
    const monat = String(heute.getMonth() + 1).padStart(2, '0');
    const tag = String(heute.getDate()).padStart(2, '0');
    const heuteFormatiert = `${jahr}-${monat}-${tag}`;

    // --- 1. OPTIMIERUNG: Workshop-Termin (Vergangenheit sperren) ---
    // Der Wunschtermin darf nicht vor dem heutigen Tag liegen
    const terminInput = document.getElementById("datum");
    if (terminInput) {
        terminInput.setAttribute("min", heuteFormatiert);
    }

    // --- 2. OPTIMIERUNG: Mindestalter 16 Jahre (Zukunft sperren) ---
    // Berechne das maximale Geburtsdatum (Heute minus genau 16 Jahre)
    const maxGeburtsJahr = jahr - 16;
    const maxGeburtsdatumFormatiert = `${maxGeburtsJahr}-${monat}-${tag}`;

    const geburtstagsInput = document.getElementById("geburtsdatum");
    if (geburtstagsInput) {
        geburtstagsInput.setAttribute("max", maxGeburtsdatumFormatiert);
    }
});