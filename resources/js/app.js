import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


const login3DContainer =
    document.getElementById(
        'login-erp-crm-3d'
    );

if (
    login3DContainer &&
    window.matchMedia(
        '(min-width: 821px)'
    ).matches
) {
    import(
        './login-3d/login-erp-crm-3d.js'
    ).catch((error) => {
        console.error(
            'SOLUTCLOUD Login 3D :',
            error
        );
    });
}
const phoneInputs = document.querySelectorAll('input[data-phone-input]');

if (phoneInputs.length > 0) {
    Promise.all([
        import('intl-tel-input/intlTelInputWithUtils'),
        import('./phone-input.js'),
    ]).then(([intlTelInputModule, phoneInputModule]) => {
        phoneInputModule.default(intlTelInputModule.default);
    }).catch((error) => {
        console.error('SOLUTCLOUD Téléphone international :', error);
    });
}
