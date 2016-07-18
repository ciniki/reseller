//
function ciniki_reseller_main() {

    this.init = function() {
        //
        // The menu panel
        //
        this.menu = new M.panel('Settings',
            'ciniki_reseller_main', 'menu',
            'mc', 'narrow', 'sectioned', 'ciniki.reseller.main.menu');
        this.menu.sections = {
//          'add':{'label':'Add', 'list':{
//              'add':{'label':'Add Business', 'fn':'M.startApp(\'ciniki.businesses.add\', null, \'M.ciniki_sysadmin_main.menu.show();\');'},
//              }},
            'tools':{'label':'Tools', 'list':{
                'passwordcards':{'label':'Password Cards', 'fn':'M.ciniki_reseller_main.passwordcardsShow(\'M.ciniki_reseller_main.showMenu();\');'},
                }},
        };
        this.menu.addButton('settings', 'Settings', 'M.startApp(\'ciniki.reseller.settings\',null,\'M.ciniki_reseller_main.showMenu();\');');
        this.menu.addClose('Back');

        //
        // The  main panel
        //
        this.passwordcards = new M.panel('Password Cards Settings',
            'ciniki_reseller_main', 'passwordcards',
            'mc', 'narrow', 'sectioned', 'ciniki.reseller.main.passwordcards');
        this.passwordcards.sections = {
            '_':{'label':'', 'fields':{
                'num_cards':{'label':'Number of Cards', 'type':'text', 'size':'small'},
                'passwords':{'label':'Passwords', 'type':'toggle', 'default':'yes', 'toggles':{'no':'No', 'yes':'Yes'}},
                }},
            '_buttons':{'label':'', 'buttons':{
                'generate':{'label':'Generate', 'fn':'M.ciniki_reseller_main.passwordcardsGenerate();'},
                }},
        };
        this.passwordcards.fieldValue = function(s, i, d) {
            if( this.data[i] == null && d.default != null ) { return d.default; }
            return this.data[i];
        };
        this.passwordcards.addClose('Back');
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
        var appContainer = M.createContainer(appPrefix, 'ciniki_reseller_main', 'yes');
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
    // show the password cards options
    //
    this.passwordcardsShow = function(cb) {
        this.passwordcards.data = {'num_cards':'1'};
        this.passwordcards.refresh();
        this.passwordcards.show(cb);
    };

    //
    // Generate the pdf
    //
    this.passwordcardsGenerate = function() {
        var args = {'business_id':M.curBusinessID};
        args['num_cards'] = M.ciniki_reseller_main.passwordcards.formValue('num_cards');
        args['passwords'] = M.ciniki_reseller_main.passwordcards.formValue('passwords');
        M.showPDF('ciniki.reseller.passwordcardsGenerate', args);
    };
}
