import { initializeApp } from "firebase/app";
import { getStorage, ref, uploadBytesResumable, getDownloadURL } from "firebase/storage";

// Configuración de Firebase
const firebaseConfig = {
  apiKey: "AIzaSyA-2DtPBr1jxLqqGLQoJ2Z5K-hH2tgr7cw",
  authDomain: "rallyfotografico-78eff.firebaseapp.com",
  projectId: "rallyfotografico-78eff",
  storageBucket: "rallyfotografico-78eff.firebasestorage.app",
  messagingSenderId: "784395544435",
  appId: "1:784395544435:web:ed78a360872d2757e18706"
};

// Inicializar Firebase
const app = initializeApp(firebaseConfig);
const storage = getStorage(app);

