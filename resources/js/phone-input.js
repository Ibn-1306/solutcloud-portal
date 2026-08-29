const translations = {
    selectedCountryAriaLabel: 'Changer le pays du numéro de téléphone, sélectionné ${countryName} (${dialCode})',
    noCountrySelected: 'Sélectionnez le pays du numéro de téléphone',
    countryListAriaLabel: 'Liste des pays',
    searchPlaceholder: 'Rechercher un pays',
    clearSearchAriaLabel: 'Effacer la recherche',
    searchEmptyState: 'Aucun pays trouvé',
};

const validationMessage = 'Saisissez un numéro valide pour le pays sélectionné.';

export default function initializePhoneInputs(intlTelInput) {
    document.querySelectorAll('input[data-phone-input]').forEach((input) => {
        if (input.dataset.phoneReady === 'true') return;

        const instance = intlTelInput(input, {
            initialCountry: input.dataset.initialCountry || 'ci',
            countryOrder: ['ci', 'sn', 'bf', 'ml', 'gh', 'tg', 'bj', 'gn', 'fr', 'ca', 'us', 'gb'],
            separateDialCode: true,
            strictMode: true,
            allowedNumberTypes: null,
            uiTranslations: translations,
        });

        input.dataset.phoneReady = 'true';
        input.autocomplete = 'tel';

        const clearValidation = () => input.setCustomValidity('');
        const validate = () => {
            clearValidation();

            if (input.value.trim() === '') return !input.required;
            if (instance.isValidNumber()) return true;

            input.setCustomValidity(validationMessage);
            return false;
        };

        input.addEventListener('input', clearValidation);
        input.addEventListener('countrychange', clearValidation);
        input.addEventListener('blur', () => {
            if (input.value.trim() !== '') validate();
        });
        input.addEventListener('phone:set-number', (event) => {
            instance.setNumber(event.detail?.number || input.value || '');
            clearValidation();
        });

        input.form?.addEventListener('submit', (event) => {
            if (!validate()) {
                event.preventDefault();
                event.stopImmediatePropagation();
                input.reportValidity();
                input.focus();
                return;
            }

            if (input.value.trim() !== '') input.value = instance.getNumber();
        }, true);

        input.form?.addEventListener('reset', () => {
            window.setTimeout(() => {
                instance.setSelectedCountry(input.dataset.initialCountry || 'ci');
                instance.setNumber('');
                clearValidation();
            });
        });
    });
}
