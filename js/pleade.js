/**
 * @See Pleade Entreprise Licence File
 */

(function ($) {

    // Pollyfill for StartsWith function (not supported in IE)
    if (!String.prototype.startsWith) {
        String.prototype.startsWith = function(searchString, position){
            position = position || 0;
            return this.substr(position, searchString.length) === searchString;
        };
    }

    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }


/**
 * Search from Drupal to Pleade and print results
 */
function buildSearchResults(){
  // try to get terms searched and collection
  var sterm = '';
  var collection = '';
  var format = '';
  var url = window.location.href;

  sterm = getParameterByName("keys", url);
  sterm_v2 = getParameterByName("query1", url);
  collection = getParameterByName("collections", url);
  format = getParameterByName("format", url);
  if( (format=='') || (format==null) || (format=='null') ) format = 'drupalall'; 
  if( (sterm=='') || (sterm==null) || (sterm=='null') ) sterm = collection = format= '';

  // update pleade block input
  if(sterm!=''){
	$('form#pleade-search-block-form input.form-text, form#pleade-search-block-form-v2 input.form-text').attr('value', sterm);
  } else if (sterm_v2!=''){
	$('form#pleade-search-block-form input.form-text, form#pleade-search-block-form-v2 input.form-text').attr('value', sterm_v2);
  }

  // process up to asked format
  if(format=='drupal') buildSearchResultsForDrupal(sterm, collection); // Print a normal Drupal search results page. It contains all (and only) Drupal results
  else if(format == 'pleade') buildSearchResultsForPleade(sterm); // Prepare Drupal search results page for injection in Pleade
  else buildSearchResultsForDrupalAll(sterm); // Inject pleade search results in Drupal
}

/**
 * Inject pleade search results in Drupal
 */
function buildSearchResultsForDrupal(sterm, collection){
  var multibase;

  // clean for unused drupal elements
  if(sterm!=''){
	$('nav.tabs, nav.pager, nav.pager-nav').remove();
	$('form#search-form.search-form').remove();
  }

  // clean for languages and encoding
  if(sterm!=''){
    sterm = decodeURIComponent( sterm );
    //sterm = sterm.substring(0, sterm.lastIndexOf(" language:"));
    sterm = sterm.trim();
  }

  // if query term is not empty, we supposed that we are on sear page results. We can start to contact Pleade
  if( (sterm != '') && (sterm!=null) ){
    sterm = encodeURIComponent( sterm );
    var qParams = "&linkBack=true&n-start=0&name=drupal&am=true&ih=false&hpp=5";
    qParams += "&cop1=AND&champ1=fulltext&query1=" + sterm;

    // First of all, we start by activating the gi provided by pleade. It permits to emphasis that the query is on loading
    if(collection != 'portal') $('#block-pleade-and-drupal-results-resume').append('<img id="search-res-tmp-loading-gif" src="/archives-en-ligne/theme/images/pnl-loading.gif" style="display: block; width: 51px; margin-left: auto; margin-right: auto;"/>');

    // Second of all we query Pleade for getting the number of results per collection except for portal. Also, main bases (ead2, ead3 and ead4) need special management
    var qurl = "/archives-en-ligne/results.json?";
    var idbresume="";
    var idbresult="";
    if( (collection == '') || (collection == null) ) {
	qurl += "base=ead2&base=ead3&base=ead4&base=militaire&base=mariage&base=recensement_nominatif&base=ecrous&base=toponyme&base=ead" + qParams;
    } else if(collection!='portal') {
	qurl += "base=" + collection + qParams;
	idbresume = 'block-'+collection+'-results-resume';
	idbresult = 'block-'+collection+'-results';
	$('.block-pleade-and-drupal-results-resume').each(function(){
		if( $(this).attr('id') != idbresume ) $(this).remove();
	});
	$('.block-pleade-and-drupal-results').each(function(){
		if( $(this).attr('id') != idbresult ) $(this).remove();
	});
    } else {
	qurl = '';
	idbresume = 'block-portal-results-resume';
	idbresult = 'block-portal-results';
	$('.block-pleade-and-drupal-results-resume').each(function(){
		if( $(this).attr('id') != idbresume ) $(this).remove();
	});
	$('.block-pleade-and-drupal-results').each(function(){
		if( $(this).attr('id') != idbresult ) $(this).remove();
	});
    }
    if(qurl!=''){
	    $.getJSON(qurl, {
		format: "json",
		cache: false
	    }).done(function( data ) {
		multibase = data.multibase;
		// clean loading in case of 0 result in Pleade
		if(multibase==null) $('#search-res-tmp-loading-gif').remove();

		// process
		for(var i in multibase ){
			var lcollection = data.multibase[i].sdxdbid
			idbresume = 'block-'+lcollection+'-results-resume';
			idbresult = 'block-'+lcollection+'-results';
			$('#'+idbresume).find('.block-pleade-and-drupal-results-resume-value').empty();
			$('#'+idbresume).find('.block-pleade-and-drupal-results-resume-value').append('<a href="#'+idbresult+'">' + data.multibase[i].nb + ' resultat(s)</a>');
		}

		// Third of all we retrieve results per base from Pleade
		for(var i in multibase ){
			var lcollection = multibase[i].sdxdbid
			var tlcollection = (lcollection.startsWith("ead")) ? "ead" : lcollection;
			idbresume = 'block-'+lcollection+'-results-resume';
			var qurl = "/archives-en-ligne/functions/"+tlcollection+"/tab-results.ajax-html?base=" + lcollection + qParams + "&divID=" + lcollection;
			if(lcollection=='ead') qurl += '&facets=flisteacte;ftypedocec;allpersname;allgeognames;fsubject;object';
			if(lcollection=='ead2') qurl += '&facets=fgeogcommune;fanciennescommune;finstitution_paroisse;ftypedocec;flisteacte';
			if(lcollection=='ead3') qurl += '&facets=fgeogcommune;fanciennescommune;fparoisses;finstitution;feglise-reformee;ftypedoccadastre';
			if(lcollection=='ead4') qurl += '&facets=fgeogcommune;fanciennescommune;fbdate';
			if(lcollection=='militaire') qurl += '&facets=nom;prenom;canton;object';
			if(lcollection=='mariage') qurl += '&facets=nom1;prenom1;nom2;prenom2;fgeogcommune;fanciennescommune;object';
			if(lcollection=='recensement_nominatif') qurl += '&facets=nom1;prenom1;nom2;fgeogcommune;fanciennescommune;nom_rue';
			if(lcollection=='ecrous') qurl += '&facets=nom1;prenom1;nom2;sexe;object';
			if(lcollection=='toponyme') qurl += '&facets=fgeogcommune;fanciennescommune;toponymes';
			$.ajax({
				url: qurl,
				cache: false,
				ti: i
			}).done(function( html ) {
				var tlcollection = multibase[this.ti].sdxdbid
				idbresult = 'block-'+tlcollection+'-results';
				$( "#"+idbresult ).append( html );
				if(i >= (multibase.length-1)) $('#search-res-tmp-loading-gif').remove();
			});
		}

	    });
    }
    // Special management for portal results
    var lcollection = 'portal';
    idbresume = 'block-'+lcollection+'-results-resume';
    idbresult = 'block-'+lcollection+'-results';
    if($('#block-pleade-and-drupal-results').length>0) $('#pl-list-results-header-portal').after( $('ol.search-results, ol.drupal-results').detach() );
    var nbportal = $('ol.search-results li, ol.drupal-results li').length;
    var portalresume = $('#'+idbresume).find('.block-pleade-and-drupal-results-resume-value').text();
    $('#'+idbresume).find('.block-pleade-and-drupal-results-resume-value').empty();
    if(nbportal > 0) {
	$('#'+idbresume).find('.block-pleade-and-drupal-results-resume-value').append('<a href="#'+idbresult+'">' + portalresume + '</a>');
	// Add link for printing all portal results
	$('#pl-list-results-details-portal a').attr('href', window.location.href.replace('format=drupal', 'format=drupalall'));
    }
    else {
	$('#'+idbresume).find('.block-pleade-and-drupal-results-resume-value').append(nbportal + ' resultat(s)');
	$('#pl-list-results-header-portal').remove();
	$('#pl-list-results-details-portal').remove()
    }

  }
}

/**
 * Print a normal Drupal search results page. It contains all (and only) Drupal results
 */
function buildSearchResultsForDrupalAll(sterm){
  // clean for unused drupal elements
  if(sterm!=''){
	$('#search-result-pleade-and-drupal-title').remove();
	$('#block-pleade-and-drupal-results-resume').remove();
	$('#block-pleade-and-drupal-results').remove();
  }
}

/**
 * Prepare Drupal search results page for injection in Pleade
 */
function buildSearchResultsForPleade(sterm){
  // Re-organize portal block
    var lcollection = 'portal';
    idbresult = 'block-'+lcollection+'-results';
    if($('#block-pleade-and-drupal-results').length>0) $('#pl-list-results-header-portal').after( $('ol.search-results, ol.drupal-results').detach() );
    $('#pl-list-results-details-portal a').attr('href', '/search/node?format=drupalall&keys='+sterm );

  // clean for unused drupal elements
  $('#pl-list-results-header-portal').remove();
  $('nav.tabs, nav.pager').remove();

  // Replace contain of body by the list of results
  var res = $('#block-portal-results');
  $('body').empty();
  $('body').append(res.html());
  
  // the considered page will be only use with iframe. So we have to use the base tag. It will permit to force link to be opened in the parent page rather than the iframe
  $('head').append('<base target="_parent" >');
  
  console.log( $('.ldrupal-list-results') );
  console.log( $('.li_drupal') );
}

/**
 * When editing a block, add a selector which contains a list of saved search and basket available; and Pleade default federate search form
 */
function alterAdminBlockEditor(){
	$('form.block-basic-form.block-content-form').each(function(){
		var mid = $(this).attr('id'); // id of this form
		var iid = $(this).find('input.text-full.form-text').attr('id'); // Contain id of the main input
		var oldInputValue = $('#' + iid).attr('value'); // contains old title

		// First of all, we signal that a query is loading
		$('#'+iid).parent().append('<img id="search-res-tmp-loading-gif-'+iid+'" src="/archives-en-ligne/theme/images/pnl-loading.gif" style="display: block; width: 51px; margin-left: auto; margin-right: auto;"/>');

		// Second of all, retrieve from pleade, the list of available saved search and basket
		$.getJSON('/pleade/functions/getSaved.json', {
			format: "json",
			cache: false
		}).done(function( data ) {
			// and initialize the selector
			var idselect = iid + '-select';
			var idbi = iid + '-input-before-pleade';
			var tselect = '<label for="'+idselect+'">Choisir une sauvegarde Pleade à associer à ce block. Pour associer à la recherche fédérée, choisir l\'option FORMULAIRE RECHERCHE FÉDÉRÉE. Pour annuler, choisir l\'option vide.</label><select id="' + idselect + '" name="'+idselect+'" class="form-select form-select-pleade-block" title="Choisir la sauvegarde ou le fomulaire à associer au block"><option value="'+oldInputValue+'"></option>';

			// process json retrived
			if( (data!=null) && (data.length>0) ){
				for( var i in data ){
					tselect += '<option value="Pleade::' + data[i].type + '::' + data[i].id +'">' + data[i].name + '</option>';
				}
			}

			// Add option for pleade federate search and close select
			tselect += '<option value="Pleade::FederateSearchForm::drupal-default">FORMULAIRE RECHERCHE FÉDÉRÉE</option>';
			tselect += '</select>';

			// remove loading
			$('#search-res-tmp-loading-gif-'+iid).remove();

			// Third of all, add created select and input in DOM
			$('#'+iid).parent().append(tselect);
			$('#'+iid).before('<input disabled="disabled" value="" id="'+ idbi +'"/>::');

			// Quarter of all, hide unused form components if option is selected
			$('#' + idselect).on('change', function(){
				//$('#' + iid).attr('value', this.value);
				if( this.value != '' ) {
					$('#' + mid).find('div.form-textarea-wrapper').parent().hide();
					$('#' + idbi).val(this.value);
					
					// hide input if pleade search form component is selected
					if( this.value.startsWith("Pleade::FederateSearchForm") ) $('#'+iid).hide();
					else $('#'+iid).show();
				}
				else {
					$('#' + mid).find('div.form-textarea-wrapper').parent().show();
					$('#' + idbi).val('');
					$('#'+iid).show();
				}
			});

		});
	});
}

/**
 * Before adminBlockEditor submited, detect if a Pleade componnent is selected and adapt the name of the actual block before send
 */
function RerouteAdminBlockEditorSubmit(){
	// detect that we are on a block page with Pleade components selector
	if( $('form.block-basic-form.block-content-form').length > 0 ){
		var mid = $('form.block-basic-form.block-content-form').attr('id'); // id of this form
		var iid = $('form.block-basic-form.block-content-form').find('input.text-full.form-text').attr('id'); // id of the main input
		var idselect = iid + '-select'; // id of the Pleade components selector
		var idbi = iid + '-input-before-pleade';
		var sid = 'edit-submit'; // id of the submit button
		
		// reroute default submit if a Pleade component is selected
		$('#'+sid).on('click', function(e){
			//e.preventDefault();
			var sval = $('#'+idbi).val();
			
			// change block name up to Pleade component selected
			if( (sval!='') && (sval!=null) && (sval!='null') ){
				e.preventDefault();
				var tsval = sval;
				if( !sval.startsWith("Pleade::FederateSearchForm") ) tsval += '::' + $('#' + iid).val();
				$('#' + iid).val(tsval);
				 $('#'+idbi).val('');
				 
				// trigger default click
				$(this).trigger('click');
			}
		});
	}
}

/**
 * Detect Saved search or saved basket or pleade default federate form block and fill them with the appropriate contain
 */
function fillSavedBlock(){
	$('.block').each(function(){
		var block = $(this);
		var bid = $(this).attr('id');

		// Management for saved
		if( (bid!=null) ){
			if( bid.startsWith('block-pleadesavedbasket') || bid.startsWith('block-pleadesavedsearch') ){
				var hpp = '6'; // Number of elements to retrieve from saved in Pleade
				var type = bid.startsWith('block-pleadesavedsearch') ? 'search' : 'basket'; // type of saved basket in Pleade
				var blockname = $('#'+bid).find('h2').text(); // Block system name in Drupal
				var savedname; // contains saved name in Pleade
				var savedid; // contains saved id
				var savedtitle; // contains druapl title
				var qurl = '/pleade/getSaved'; // URL for getting block content from Pleade
				
				// First all we build the name of saved in Pleade and dedicated embedded URL
				if(type == 'search'){
					savedname = blockname.substring( blockname.indexOf('Pleade::SavedSearch::')+21 );
					savedid = savedname.split('::')[0];
					savedtitle = savedname.split('::')[1];
					qurl += 'Search.ajax-html?id=' + savedid;
				} else {
					savedname = blockname.substring( blockname.indexOf('Pleade::SavedBasket::')+21 );
					savedid = savedname.split('::')[0];
					savedtitle = savedname.split('::')[1];
					qurl += 'Basket.ajax-html?id=' + savedid;
				}
				qurl += '&hpp=' + hpp;
	
				// Second of all, Prepare before query Pleade
				$('#'+bid).append('<img id="search-res-tmp-loading-gif-'+bid+'" src="/archives-en-ligne/theme/images/pnl-loading.gif" style="display: block; width: 51px; margin-left: auto; margin-right: auto;"/>');
				//$('#'+bid).find('div.content, div.contextual').empty();
	
				// Third of all, query Pleade
				$.ajax({
					type: "POST",
					url: qurl,
					dataType: "xml"
				}).done(function( xml ) {
					$('#'+bid).find('h2').hide();
					
					// add bloc for drupal title and link for page print all
					var salllink = '/archives-en-ligne/getSaved' + ( (type=='search')?'Search':'Basket' ) + '.html?id=' + savedid;
					block.prepend('<div class="pl-list-results-block-up pl-list-results-block-up-drupal pl-list-results-block-up-basket-and-search"><h2 class="pl-title">'+savedtitle+'</h2><div class="pl-view-all-link"><a href="'+salllink+'" alt="voir les résultats">Voir toutes les publications</a>');
					
					$('#'+bid).append( $(xml).find('div.pl-list-results') );
					$('#'+'search-res-tmp-loading-gif-'+bid).remove();
				});
			} else if( bid.startsWith('block-pleadefederatesearchform') ){ // management for pleade federate searchform
				var blockname = $('#'+bid).find('h2').text(); // Block system name in Drupal
				var savedname; // contain saved name in Pleade
				var qurl = '/pleade/embed/'; // URL for getting block content from Pleade
				
				// Before start, add default class for pleade search form block
				$(this).addClass('header-block-search block-pleade block-pleade-search-form-block block-pleade-search-form-block-v2');
				
				// First all we build the name of saved in Pleade and dedicated embedded URL
				//savedname = blockname.substring( blockname.indexOf('Pleade::FederateSearchForm::')+28 );
				savedname = 'drupal-default';
				qurl += savedname + '-search-form.xsp';
	
				// Second of all, Prepare before query Pleade
				$('#'+bid).append('<img id="search-res-tmp-loading-gif-'+bid+'" src="/archives-en-ligne/theme/images/pnl-loading.gif" style="display: block; width: 51px; margin-left: auto; margin-right: auto;"/>');
	
				// Third of all, query Pleade
				$.ajax({
					url: qurl,
					dataType: "xml",
				}).done(function( xml ) {
					$('#'+bid).find('h2').hide();
					$('#'+bid).append( $(xml).find('div#main-search-form-'+savedname) );
					$('#'+'search-res-tmp-loading-gif-'+bid).remove();
				});
			}
		}
	});
}

/******************************
 * Trigger functions section  *
 *****************************/
buildSearchResults();
alterAdminBlockEditor();
RerouteAdminBlockEditorSubmit();
fillSavedBlock();

	// Corrige problème css IE
	if( ($(window).width() >= 900) && ($(window).width() <= 1024) ) $('.node-type-page-d-accueil-des-rubriques .group-rubriques .field-items .even + .odd + .even, .node-type-page-d-accueil-des-rubriques .group-rubriques .field-items .even + .odd + .even + .odd + .even + .odd, .node-type-page-d-accueil-des-rubriques .group-rubriques .field-items .even + .odd + .even + .odd + .even + .odd + .even + .odd + .even, .node-type-page-d-accueil-des-expositions .group-rubriques .field-items .even + .odd + .even, .node-type-page-d-accueil-des-expositions .group-rubriques .field-items .even + .odd + .even + .odd + .even + .odd, .node-type-page-d-accueil-des-expositions .group-rubriques .field-items .even + .odd + .even + .odd + .even + .odd + .even + .odd + .even').css("margin-right", "25px");

})(jQuery);
