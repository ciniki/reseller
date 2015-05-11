<?php
//
// Description
// ===========
// This method returns a PDF of password cards for printing and signing up new businesses.
//
// Arguments
// ---------
// 
// Returns
// -------
//
function ciniki_reseller_passwordcardsGenerate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'num_cards'=>array('required'=>'no', 'blank'=>'no', 'default'=>'1', 'name'=>'Number of Cards'), 
        'passwords'=>array('required'=>'no', 'blank'=>'no', 'default'=>'no', 'name'=>'Passwords'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];

    //  
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'reseller', 'private', 'checkAccess');
    $rc = ciniki_reseller_checkAccess($ciniki, $args['business_id'], 'ciniki.reseller.passwordcardsGenerate'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

	$rc = ciniki_core_loadMethod($ciniki, 'ciniki', 'reseller', 'templates', 'passwordcards');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$fn = $rc['function_call'];
	$rc = $fn($ciniki, $args['business_id'], $args);
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}

	if( isset($rc['pdf']) ) {
		$rc['pdf']->Output('passwordcards.pdf', 'D');
	}

	return array('stat'=>'exit');
}
?>
