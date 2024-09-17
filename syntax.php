<?php
if (!defined('DOKU_INC')) die();

class syntax_plugin_fho extends DokuWiki_Syntax_Plugin {

    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'block';
    }

    public function getSort() {
        return 10;
    }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{fho}}', $mode, 'plugin_fho');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler) {
        return array();
    }

    public function render($mode, Doku_Renderer $renderer, $data) {
    if ($mode === 'xhtml') {
        // Hier fügen wir sicherheitshalber das CSS direkt hinzu
        $renderer->doc .= '<style>
            table.status-table {
                width: 100%;
                max-width: 100%;
                border-collapse: collapse;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
                overflow: auto;
                display: block;
            }
            table.status-table th, table.status-table td {
                padding: 10px;
                border: 1px solid #ddd;
                text-align: center;
                white-space: nowrap;
            }
            table.status-table th {
                background-color: #f5f5f5;
                font-weight: bold;
            }
            table.status-table td {
                background-color: #fff;
                transition: background-color 0.3s ease;
            }
            table.status-table td:hover {
                background-color: #eaeaea;
            }
            .legend {
                display: flex;
                gap: 15px;
                margin-top: 15px;
            }
            .legend span {
                display: inline-block;
                width: 20px;
                height: 20px;
                border-radius: 3px;
                cursor: pointer;
            }
            .legend-blue { background-color: blue; }
            .legend-green { background-color: green; }
            .legend-yellow { background-color: yellow; }
            .legend-red { background-color: red; }
            .legend-delete {
                background-color: #fff;
                border: 1px solid black;
            }
            button#save-button {
                margin-top: 10px;
                padding: 10px 15px;
                background-color: #007bff;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }
            button#save-button:hover {
                background-color: #0056b3;
                transform: scale(1.05);
            }
        </style>';

        // Dann die HTML-Tabelle
        $renderer->doc .= '<div id="statusanzeige">';
        $renderer->doc .= $this->generateTable();
        $renderer->doc .= '<button id="save-button">Speichern</button>';
        $renderer->doc .= '<button id="load-button" style="display:none;">Laden</button>';
        $renderer->doc .= '</div>';
    }
    return true;
}

    private function generateTable() {
    $employees = $GLOBALS['EMPLOYEE_LIST'];
    $html = '<table class="status-table">';
    $html .= '<thead><tr><th>Name</th>';

    for ($i = 6; $i <= 22; $i++) {
        $html .= "<th>$i:00</th><th>$i:15</th><th>$i:30</th><th>$i:45</th>";
    }

    $html .= '</tr></thead><tbody>';
    foreach ($employees as $employee) {
        $html .= "<tr><td>$employee</td>";
        for ($i = 6; $i <= 22; $i++) {
			for ($j = 0; $j < 4; $j++) {
        $html .= '<td class="status-cell" data-status=""></td>';  // Zellen mit data-status
			}
		}
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    // Farbcode-Legende hinzufügen
    $html .= '<div class="legend">';
    $html .= '<p><span class="legend-blue" style="background-color: blue; border: 1px solid black; display: inline-block; width: 20px; height: 20px;"></span> Anwesend in Präsenz</p>';
    $html .= '<p><span class="legend-green" style="background-color: green; border: 1px solid black; display: inline-block; width: 20px; height: 20px;"></span> Im mobilen Homeoffice erreichbar und aktiv</p>';
    $html .= '<p><span class="legend-yellow" style="background-color: yellow; border: 1px solid black; display: inline-block; width: 20px; height: 20px;"></span> Im mobilen Homeoffice nicht erreichbar</p>';
    $html .= '<p><span class="legend-red" style="background-color: red; border: 1px solid black; display: inline-block; width: 20px; height: 20px;"></span> Tel. erreichbar aber inaktiv</p>';
    $html .= '<p><span class="legend-delete" style="background-color: #fff; border: 1px solid black; display: inline-block; width: 20px; height: 20px; cursor:pointer;"></span> Farbe löschen</p>';
    $html .= '</div>';

    return $html;
	}
}
?>
