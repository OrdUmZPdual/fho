document.addEventListener("DOMContentLoaded", function() {
    let selectedColor = 'blue';  // Standardmäßig blau
    let isDragging = false;      // Um das Ziehen zu erkennen
    let currentRow = null;       // Um die aktuelle Zeile zu speichern

    // EventListener für die Farbauswahl in der Legende
    const legendItems = document.querySelectorAll(".legend span");
    legendItems.forEach(item => {
        item.addEventListener("click", function() {
            selectedColor = getComputedStyle(item).backgroundColor;  // Setzt die gewählte Farbe
        });
    });

    // EventListener für den Löschbereich in der Legende
    const deleteButton = document.querySelector(".legend-delete");
    deleteButton.addEventListener("click", function() {
        selectedColor = '';  // Setzt die gewählte Farbe auf leer (löschen)
    });

    // EventListener für die Statuszellen
    const statusRows = document.querySelectorAll("table.status-table tbody tr");  // Wähle alle Zeilen

    statusRows.forEach(row => {
        const cells = row.querySelectorAll(".status-cell");
        cells.forEach(cell => {
            cell.addEventListener("click", function() {
                setStatusColor(cell, selectedColor);
            });

            cell.addEventListener("mousedown", function() {
                isDragging = true;
                currentRow = cell.parentElement;  // Speichert die aktuelle Zeile
                setStatusColor(cell, selectedColor);
            });

            cell.addEventListener("mouseover", function() {
                if (isDragging && cell.parentElement === currentRow) {
                    setStatusColor(cell, selectedColor);
                }
            });

            cell.addEventListener("mouseup", function() {
                isDragging = false;
                currentRow = null;  // Setzt die Zeile zurück
            });
        });
    });

    // Funktion zum Setzen der Farbe
    function setStatusColor(cell, color) {
        cell.style.backgroundColor = color;
        cell.setAttribute("data-status", color);
    }

    // Funktion zum Speichern der Daten, nur wenn die Zelle eine Farbe hat
    function saveData() {
        let statusData = [];
        statusRows.forEach((row, rowIndex) => {
            const cells = row.querySelectorAll(".status-cell");
            cells.forEach((cell, cellIndex) => {
                const color = cell.getAttribute("data-status");
                if (color) {  // Nur speichern, wenn die Zelle eine Farbe hat
                    statusData.push({
                        row: rowIndex,
                        column: cellIndex,
                        color: color
                    });
                }
            });
        });

        fetch('lib/plugins/fho/save_status.php', {  // Speichere direkt in save_status.php
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(statusData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Daten erfolgreich gespeichert');
            } else {
                alert('Fehler beim Speichern der Daten: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Fehler bei der Anfrage:', error);
        });
    }

    // EventListener für den Speichern-Button
    const saveButton = document.getElementById("save-button");
    saveButton.addEventListener("click", saveData);

    // Automatisch den Lade-Button klicken, aber unsichtbar machen
    const loadButton = document.getElementById("load-button");
    loadButton.style.display = 'none';  // Button unsichtbar machen
    loadData();  // Automatisch Daten laden

    // Funktion zum Laden der Daten
    function loadData() {
        fetch('lib/plugins/fho/save_status.php', {  // Abrufen der Daten via GET
            method: 'GET',
        })
        .then(response => {
            if (!response.ok) {  // Prüfe, ob die Antwort erfolgreich war
                throw new Error('Netzwerkantwort war nicht ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Geladene Daten:', data);  // Ausgabe der empfangenen Daten in der Konsole
            if (data && data.length > 0) {
                data.forEach(entry => {
                    const rowIndex = entry.row;
                    const cellIndex = entry.column;
                    const color = entry.color;

                    console.log(`Zeile: ${rowIndex}, Spalte: ${cellIndex}, Farbe: ${color}`);  // Debugging-Ausgabe
                    const row = statusRows[rowIndex];  // Hole die entsprechende Zeile
                    const cell = row.querySelectorAll(".status-cell")[cellIndex];  // Hole die entsprechende Zelle
                    if (cell) {
                        setStatusColor(cell, color);  // Setze die Farbe der Zelle
                    } else {
                        console.error('Zelle nicht gefunden:', rowIndex, cellIndex);  // Ausgabe, falls Zelle nicht gefunden wurde
                    }
                });
            }
        })
        .catch(error => {
            console.error('Fehler beim Laden der Daten:', error);
        });
    }
});
