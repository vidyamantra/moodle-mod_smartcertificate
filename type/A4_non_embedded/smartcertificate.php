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
 * A4_non_embedded smartcertificate type
 *
 * @package    mod_smartcertificate
 * @copyright  Vidya Mantra EduSystems Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$pdf = new PDF($smartcertificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($smartcertificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables.
// Landscape.
if ($smartcertificate->orientation == 'L') {
    $x = 10;
    $y = 30;
    $sealx = 230;
    $sealy = 150;
    $sigx = 47;
    $sigy = 155;
    $custx = 47;
    $custy = 155;
    $wmarkx = 40;
    $wmarky = 31;
    $wmarkw = 212;
    $wmarkh = 148;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 175;
} else { // Portrait.
    $x = 10;
    $y = 40;
    $sealx = 150;
    $sealy = 220;
    $sigx = 30;
    $sigy = 230;
    $custx = 30;
    $custy = 230;
    $wmarkx = 26;
    $wmarky = 58;
    $wmarkw = 158;
    $wmarkh = 170;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 210;
    $brdrh = 297;
    $codey = 250;
}

// Add images and lines.
smartcertificate_print_image($pdf, $smartcertificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
smartcertificate_draw_frame($pdf, $smartcertificate);
// Set alpha to semi-transparency.
$pdf->SetAlpha(0.2);
smartcertificate_print_image($pdf, $smartcertificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
smartcertificate_print_image($pdf, $smartcertificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
smartcertificate_print_image($pdf, $smartcertificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text.
$pdf->SetTextColor(0, 0, 120);
smartcertificate_print_text($pdf, $x, $y, 'C', 'Helvetica', '', 30, get_string('title', 'smartcertificate'));
$pdf->SetTextColor(0, 0, 0);
smartcertificate_print_text($pdf, $x, $y + 20, 'C', 'Times', '', 20, get_string('certify', 'smartcertificate'));
smartcertificate_print_text($pdf, $x, $y + 36, 'C', 'Helvetica', '', 30, fullname($USER));
smartcertificate_print_text($pdf, $x, $y + 55, 'C', 'Helvetica', '', 20, get_string('statement', 'smartcertificate'));
smartcertificate_print_text($pdf, $x, $y + 72, 'C', 'Helvetica', '', 20, format_string($course->fullname));
smartcertificate_print_text($pdf, $x, $y + 92, 'C', 'Helvetica', '', 14, smartcertificate_get_date($smartcertificate, $certrecord, $course));
smartcertificate_print_text($pdf, $x, $y + 102, 'C', 'Times', '', 10, smartcertificate_get_grade($smartcertificate, $course));
smartcertificate_print_text($pdf, $x, $y + 112, 'C', 'Times', '', 10, smartcertificate_get_outcome($smartcertificate, $course));
if ($smartcertificate->printhours) {
    smartcertificate_print_text($pdf, $x, $y + 122, 'C', 'Times', '', 10, get_string('credithours', 'smartcertificate') . ': ' . $smartcertificate->printhours);
}
smartcertificate_print_text($pdf, $x, $codey, 'C', 'Times', '', 10, smartcertificate_get_code($smartcertificate, $certrecord));
$i = 0;
if ($smartcertificate->printteacher) {
    $context = context_module::instance($cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/smartcertificate:printteacher', '', $sort = 'u.lastname ASC', '', '', '', '', false)) {
        foreach ($teachers as $teacher) {
            $i++;
            smartcertificate_print_text($pdf, $sigx, $sigy + ($i * 4), 'L', 'Times', '', 12, fullname($teacher));
        }
    }
}

smartcertificate_print_text($pdf, $custx, $custy, 'L', null, null, null, $smartcertificate->customtext);
