define([
    'jquery',
    'ko',
    'jquery/ui',
], function ($, ko) {
    'use strict';

    let companiesList = [{label: '', value: ''}];
    let companyId = ko.observable();
    let companyName = ko.observable();
    
    ko.bindingHandlers.companyAutoComplete = {
        init: function (element, params) {
            $(element).autocomplete(params());
        },
        update: function (element, params) {
            $(element).autocomplete("option", "source", params().source);
        }
    };

    /**
     * @param {Event} 
     * @param {Object} item
     */
    function selectCompany(event, ui) {
        event.preventDefault();
        if(typeof ui.item !== "undefined") {
            companyName(ui.item.label);
            companyId(ui.item.value);
        } 
    }

    /**
     * @return {Array} 
     */
    function getCompaniesList() {
        return companiesList;
    }

    /**
     * @param {Array} 
     */
    function setCompaniesList(companies) {
        companiesList = companies;
    }
    
    return {
        selectCompany: selectCompany,
        getCompaniesList: getCompaniesList,
        setCompaniesList: setCompaniesList,
        companyId: companyId,
        companyName: companyName
    };
});
