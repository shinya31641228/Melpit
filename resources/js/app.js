require('./bootstrap');

import { library, dom } from '@fortawesome/fontawesome-svg-core'
import { faAddressCard, faClock } from '@fortawesome/free-regular-svg-icons'
import { faSearch, faStoreAlt, faShoppingBag, faSignOutAlt, faYenSign, faCamera } from '@fortawesome/free-solid-svg-icons'

library.add(faSearch, faAddressCard, faStoreAlt, faShoppingBag, faSignOutAlt, faYenSign, faClock, faCamera);

dom.watch();

document.querySelector('.image-picker input')
  .addEventListener('change', (e) => {
    // ここに画像が選択された時の処理を記述する
    const input = e.target;
    const reader = new FileReader();
    reader.onload = (e) => {
      // ここに、画像を読み込んだ後の処理を記述する
      input.closest('.image-picker').querySelector('img').src = e.target.result
    };
    reader.readAsDataURL(input.files[0]);
  });
