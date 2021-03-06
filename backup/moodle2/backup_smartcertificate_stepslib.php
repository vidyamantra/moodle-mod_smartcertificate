<?php
// This file is part of the Smart Certificate module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    mod_smartcertificate
 * @subpackage backup-moodle2
 * @copyright Vidya Mantra EduSystems Pvt. Ltd.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_smartcertificate_activity_task
 */

/**
 * Define the complete smartcertificate structure for backup, with file and id annotations
 */
class backup_smartcertificate_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $smartcertificate = new backup_nested_element('smartcertificate', array('id'), array(
            'name', 'intro', 'introformat', 'emailteachers', 'emailothers',
            'savecert', 'reportcert', 'delivery', 'smartcertificatetype', 'orientation',
            'borderstyle', 'bordercolor', 'printwmark', 'printdate', 'datefmt', 'printnumber',
            'printgrade', 'gradefmt', 'printoutcome', 'printhours', 'printteacher', 'customtext',
            'printsignature', 'printseal', 'timecreated', 'timemodified', 'companyid', 'certificationname', 'certificationurl', 'licensenumber', 'linkedincheckbox'));

        $issues = new backup_nested_element('issues');

        $issue = new backup_nested_element('issue', array('id'), array(
            'smartcertificateid', 'userid', 'timecreated', 'code'));

        // Build the tree.
        $smartcertificate->add_child($issues);
        $issues->add_child($issue);

        // Define sources.
        $smartcertificate->set_source_table('smartcertificate', array('id' => backup::VAR_ACTIVITYID));

        // All the rest of elements only happen if we are including user info.
        if ($userinfo) {
            $issue->set_source_table('smartcertificate_issues', array('smartcertificateid' => backup::VAR_PARENTID));
        }

        // Annotate the user id's where required.
        $issue->annotate_ids('user', 'userid');

        // Define file annotations.
        $smartcertificate->annotate_files('mod_smartcertificate', 'intro', null); // This file area hasn't itemid.
        $issue->annotate_files('mod_smartcertificate', 'issue', 'id');

        // Return the root element (smartcertificate), wrapped into standard activity structure.
        return $this->prepare_activity_structure($smartcertificate);
    }
}
