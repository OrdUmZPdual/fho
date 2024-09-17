<?php
if (!defined('DOKU_INC')) die();

class action_plugin_fho extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_employee_configuration');
    }

    public function handle_employee_configuration(Doku_Event $event, $param) {
        // Read the employees from the DokuWiki configuration (comma separated string)
        $employeeList = $this->getConf('employees');
        if ($employeeList) {
            $employees = array_map('trim', explode(',', $employeeList));  // Convert to array
        } else {
            $employees = ['Mitarbeiter 1', 'Mitarbeiter 2', 'Mitarbeiter 3'];  // Default employees
        }

        // Make the employee list available for the syntax plugin to display in the table
        $GLOBALS['EMPLOYEE_LIST'] = $employees;
    }
}
?>
