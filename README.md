# Sistem Hakkında

Bu sistemde Zend Framework kullanılmıştır. Sb-admin teması entegre edilmiştir. 
Db olarak mysql kullanılmaktadır. Sistemin amacı verilen 4 takımı simülate
ederek fikstür oluşturup, bu 4 takımı haftalara göre puan cetvelinde gösterip,
şampiyon takımı ve diğer takımların durumlarını görmektir.

Sistemde takımların yenme yenilme algoritması şu şekildedir :
 Her takımın bir gücü vardır. Mesela GS 80, İBB 40 olsun. Sistem GS için random ile 0-80 arasında bir sayı
 üretmektedir. İBB için de 0-40 arasında. Farzedelim GS için 47, İBB için de 34 üretti. Bunların int şekilde 10 sayısına bölümünü alıyoruz. Elde ettiğimiz sonuc 4-3. GS İBB'yi 4-3 yenmiştir.

### Sisteminize Yüklemek İçin :
  Apache,Mysql,Php kurulu olmalıdır.
  Bu kodlar web klasörünüze bir dizin halinde atılmalıdır(ubuntu deb : /var/www , windows :C://wamp/www vs)
  
  Virtual host ayarlarının yapılması gerekmektedir.
  
  Ubuntu icin :
   ~/etc/hosts dosyanıza 127.0.0.1	bu.benim.linkim gibi eklenmesi gerekmektedir.
   Sonra /etc/apache2/sites-available/klasöründe bu.benim.linkim şeklinde dosya açılmalıdır.
  
  <VirtualHost *:80>
     ServerName is.is.is
     DocumentRoot /var/www/is/public
     <Directory /var/www/is/public>
         DirectoryIndex index.php
         AllowOverride All
         Order allow,deny
         Allow from all
     </Directory>
  </VirtualHost>
