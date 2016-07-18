<?php
//
// Description
// -----------
// This method will turn the reseller settings for a business.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:     The ID of the business to get the ATDO settings for.
// 
// Returns
// -------
//
function ciniki_reseller_settingsGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
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
    $rc = ciniki_reseller_checkAccess($ciniki, $args['business_id'], 'ciniki.reseller.settingsGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }
    $modules = $rc['modules'];
    
    //
    // Grab the settings for the business from the database
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDetailsQuery');
    $rc = ciniki_core_dbDetailsQuery($ciniki, 'ciniki_reseller_settings', 'business_id', $args['business_id'], 'ciniki.reseller', 'settings', '');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    if( !isset($rc['settings']) ) {
        return array('stat'=>'ok', 'settings'=>array());
    }
    $settings = $rc['settings'];

    return array('stat'=>'ok', 'settings'=>$settings);
}
?>
