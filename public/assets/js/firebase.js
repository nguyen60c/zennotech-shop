$(document).ready(function(){
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
    const firebaseConfig = {
        apiKey: "AIzaSyBqbn9xKG9v6k3hAsii_9D_j9hqVRw-2DY",
        authDomain: "zenno-shopping-demo.firebaseapp.com",
        projectId: "zenno-shopping-demo",
        storageBucket: "zenno-shopping-demo.appspot.com",
        messagingSenderId: "496921670656",
        appId: "1:496921670656:web:cce1e31b77d9dae2fb14e5",
        measurementId: "G-BE18CE47M1"
    };

    firebase.initializeApp(firebaseConfig);

    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha', {
        'callback': (response) => {
            // reCAPTCHA solved, allow signInWithPhoneNumber.
            // ...
        },
        'expired-callback': () => {
            // Response expired. Ask user to solve reCAPTCHA again.
            // ...
        }
    })
    window.recaptchaVerifier.render()
});
