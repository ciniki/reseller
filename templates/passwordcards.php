<?php
//
// Description
// -----------
// This function produces 1 or more 3x5 password cards for use when signing up new tenants.
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_reseller_templates_passwordcards($ciniki, $tnid, $args) {

    require_once($ciniki['config']['ciniki.core']['lib_dir'] . '/tcpdf/tcpdf.php');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'loadCacheJPEG');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'tenants', 'private', 'tenantDetails');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDetailsQueryDash');

    //
    // Load tenant details
    //
    $rc = ciniki_tenants_tenantDetails($ciniki, $tnid);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['details']) && is_array($rc['details']) ) {   
        $tenant_details = $rc['details'];
    } else {
        $tenant_details = array();
    }

    //
    // Load the settings for reseller
    //
    $rc = ciniki_core_dbDetailsQueryDash($ciniki, 'ciniki_reseller_settings', 'tnid', $tnid, 'ciniki.reseller', 'settings', 'passwordcards');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['settings']) ) {
        $settings = $rc['settings'];
    } else {
        $settings = array();
    }

    //
    // Create a custom class for this document
    //
    class MYPDF extends TCPDF {
        public $header_image_id = 0;
        public $footer_text = '';
        public $header_height = 20;
        public $left_margin = 10;
        public $top_margin = 8;
        public $right_margin = 10;
        public $bottom_margin = 5;

        public function Header() {
        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            // Set font
        }

        public function AddCard($ciniki, $tnid, $password) {
            $this->AddPage();
            $this->SetFillColor(255);
            $this->SetTextColor(0);
            $this->SetDrawColor(51);
            $this->SetLineWidth(0.15);
            
            if( $this->header_image_id > 0 ) {
                $rc = ciniki_images_loadCacheJPEG($ciniki, $tnid, $this->header_image_id, 2000, 2000);
                if( $rc['stat'] == 'ok' ) { 
                    $image = $rc['image'];
                    $img_box_width = ($this->getPageWidth() - $this->left_margin - $this->right_margin);
                    $this->SetLineWidth(0.25);
                    $this->SetDrawColor(50);
                    $img = $this->Image('@'.$image, '', '', $img_box_width, $this->header_height, 'JPEG', '', '', false, 300, '', false, false, 0, 'CT');
                }
            }
        
            $this->SetFont('helvetica', '', 16);
            $this->SetY(32);
            $this->Cell(35, 14, 'username: ', 0, 0, 'R');
            $this->Ln(12);
            $this->Cell(35, 14, 'password: ', 0, 0, 'R');
            $this->SetFont('courier', '', 16);
            $this->setFontSpacing(0.75);
            $this->Cell(35, 14, '  ' . $password, 0, 0, 'L');
            $this->setFontSpacing(0);
        
            // Footer
            $this->SetY(60);
            $this->SetTextColor(128);
            $this->SetFont('helvetica', '', 14);
            $this->Cell(0, 14, $this->footer_text, 0, false, 'C', 0, '', 0, false, 'T', 'M');
            $this->endPage();
        }
    }

    //
    // Start a new document, with 3.00 x 5.00 in paper (index cards)
    //
    $pdf = new MYPDF('L', 'mm', array(76.200, 127.000), true, 'UTF-8', false);

    // Set PDF basics
    $pdf->SetCreator('Ciniki');
    $pdf->SetAuthor($tenant_details['name']);
    $pdf->footer_text = $tenant_details['name'];
    $pdf->SetTitle('Password Cards');
    $pdf->SetSubject('');
    $pdf->SetKeywords('');

    $pdf->SetMargins($pdf->left_margin, $pdf->top_margin, $pdf->right_margin);
//  $pdf->setPageOrientation('L', false);
    $pdf->SetAutoPageBreak(false);
    $pdf->SetFooterMargin(0);

    if( isset($settings['passwordcards-image']) && $settings['passwordcards-image'] > 0 ) { 
        $pdf->header_image_id = $settings['passwordcards-image'];
    }
    if( isset($settings['passwordcards-footer-message']) && $settings['passwordcards-footer-message'] != '' ) { 
        $pdf->footer_text = $settings['passwordcards-footer-message'];
    }

    // Set font
    $pdf->SetFont('times', '', 14);
    $pdf->SetCellPadding(0);

    $num_cards = 1;
    if( isset($args['num_cards']) && $args['num_cards'] > 1 ) { 
        $num_cards = $args['num_cards'];
    }

//  $chars = 'ABCDEF2GHJK34MN56PQ789RS2TU3VWX654YZa9bc8def7ghj6kmnpqrstuvwxyz23456789';
    // Available characters for passwords
    $chars1 = 'abcdefghjkmnpqrstuvwxyz';
    $chars2 = 'abcdefghjkmnpqrstuvwxyz23456789';
    for($i = 0; $i < $num_cards; $i++) {
        $password = '';
        if( isset($args['passwords']) && $args['passwords'] == 'yes' ) {
            $password .= substr($chars1, rand(0, strlen($chars1)-1), 1);
            for($j=0;$j<9;$j++) {
                $password .= substr($chars2, rand(0, strlen($chars2)-1), 1);
            }
        }
        $pdf->AddCard($ciniki, $tnid, $password);
    }

    return array('stat'=>'ok', 'pdf'=>$pdf);
}
?>
