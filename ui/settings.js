//
function ciniki_reseller_settings() {

    this.init = function() {
        //
        // The menu panel
        //
        this.menu = new M.panel('Settings',
            'ciniki_reseller_settings', 'menu',
            'mc', 'narrow', 'sectioned', 'ciniki.reseller.settings.menu');
        this.menu.sections = {
            '_':{'label':'', 'list':{
                'passwordcards':{'label':'Password Cards', 'fn':'M.ciniki_reseller_settings.passwordcardsShow(\'M.ciniki_reseller_settings.showMenu();\');'},
                }},
        };
        this.menu.addClose('Back');

        //
        // The  settings panel
        //
        this.passwordcards = new M.panel('Password Cards Settings',
            'ciniki_reseller_settings', 'passwordcards',
            'mc', 'medium', 'sectioned', 'ciniki.reseller.settings.passwordcards');
        this.passwordcards.sections = {
            'image':{'label':'Header Image', 'fields':{
                'passwordcards-image':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no'},
                }},
            '_footer_msg':{'label':'Footer Message', 'fields':{
                'passwordcards-footer-message':{'label':'', 'hidelabel':'yes', 'type':'text'},
                }},
            '_buttons':{'label':'', 'buttons':{
                'save':{'label':'Save', 'fn':'M.ciniki_reseller_settings.passwordcardsSave();'},
                }},
        };
        this.passwordcards.fieldHistoryArgs = function(s, i) {
            return {'method':'ciniki.reseller.settingsHistory', 
                'args':{'business_id':M.curBusinessID, 'setting':i}};
        }
        this.passwordcards.fieldValue = function(s, i, d) {
            if( this.data[i] == null && d.default != null ) { return d.default; }
            return this.data[i];
        };
        this.passwordcards.addDropImage = function(iid) {
            M.ciniki_reseller_settings.passwordcards.setFieldValue('passwordcards-image', iid);
            return true;
        };
        this.passwordcards.deleteImage = function(fid) {
            this.setFieldValue(fid, 0);
            return true;
        };
        this.passwordcards.addButton('save', 'Save', 'M.ciniki_reseller_settings.passwordcardsSave();');
        this.passwordcards.addClose('Cancel');
    }

    //
    // Arguments:
    // aG - The arguments to be parsed into args
    //
    this.start = function(cb, appPrefix, aG) {
        args = {};
        if( aG != null ) { args = eval(aG); }

        //
        // Create the app container if it doesn't exist, and clear it out
        // if it does exist.
        //
        var appContainer = M.createContainer(appPrefix, 'ciniki_reseller_settings', 'yes');
        if( appContainer == null ) {
            alert('App Error');
            return false;
        } 

        this.showMenu(cb);
    }

    //
    // Grab the stats for the business from the database and present the list of orders.
    //
    this.showMenu = function(cb) {
        this.menu.refresh();
        this.menu.show(cb);
    }

    //
    // show the paypal settings
    //
    this.passwordcardsShow = function(cb) {
        M.api.getJSONCb('ciniki.reseller.settingsGet', {'business_id':M.curBusinessID}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_reseller_settings.passwordcards;
            p.data = rsp.settings;
            p.refresh();
            p.show(cb);
        });
    };

    //
    // Save the Paypal settings
    //
    this.passwordcardsSave = function() {
        var c = this.passwordcards.serializeForm('no');
        if( c != '' ) {
            M.api.postJSONCb('ciniki.reseller.settingsUpdate', {'business_id':M.curBusinessID}, 
                c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    M.ciniki_reseller_settings.passwordcards.close();
                });
        } else {
            this.passwordcards.close();
        }
    };
}
