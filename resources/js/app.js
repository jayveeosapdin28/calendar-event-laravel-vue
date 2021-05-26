require('./bootstrap');
import { createApp} from  'vue'
import App from "./components/App";
import axios from 'axios'
import VueAxios from 'vue-axios'


createApp(App)
        .use(VueAxios, axios)
        .mount("#app");
