define([
    'ko',
], function (ko) {
    'use strict';

    let state = {
        email: ko.observable(''),
        dni: ko.observable(''),
        selectedOptionLabel: ko.observable(''),
        isEmailAvailable: ko.observable(false),
        isDni: ko.observable(false),
        isEmail: ko.observable(false),
        checkedMethod: ko.observable(''),
        checkedDomain: ko.observable(''),
        companyDomains: ko.observableArray(),
        errorMessage: ko.observable(''),
    };

    function subscribeToObservables() {
        state.checkedMethod.subscribe(function(selecteOption) {
            if(selecteOption == 'dni') {
                state.selectedOptionLabel('DNI');
                state.isEmail(false);
                state.isDni(true);
            } else if(selecteOption == 'email') {
                state.selectedOptionLabel('Email')
                state.isEmail(true);
                state.isDni(false);
            }
        }, this);
        state.companyDomains.subscribe(function(domains) {
            if(domains.length != 0) {
                state.isEmailAvailable(true);
                state.checkedDomain(domains[0]);
            } else {
                state.isEmailAvailable(false);
            }
        }, this);
    }

    function validateDigitsOnly(data, event) {
        event.srcElement.value = event.srcElement.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    }

    function validateDniLength() {
        console.log(state.dni().length);
        return state.dni().length >= 7 && state.dni().length <= 8;
    }

    function isInputValid() {
        state.errorMessage('');
        let message = '';

        if(!state.isEmail && !state.isDni) {
            message = 'Por favor, selecciones una opción';
        } else if(state.isEmail() && !state.email()) {
            message = 'Por favor, especifique un email';
        } else if(state.isDni() && !state.dni()) {
            message = 'Por favor ingrese un DNI';
        } else  if(state.isDni() && !validateDniLength()) {
            message = 'Por favor ingrese un DNI válido';
        }

        if(message) {
            state.errorMessage(message);
            return false;
        }
        return true;
    }

    return {
        state: state,
        subscribeToObservables: subscribeToObservables,
        validateDigitsOnly: validateDigitsOnly,
        isInputValid: isInputValid,
    };
});
