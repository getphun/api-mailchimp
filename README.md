# api-mailchimp

Modul ini membutuhkan informasi mailchimp API key, dan mailchimp list id yang
disimpan di [site param](https://github.com/getphun/site-param) dengan nama 
`mailchimp_app_key` dan `mailchimp_list_id`.

Module ini mendaftarkan satu service dengan nama `mc` yang bisa diakses dari
kontroler dengan perintah `$this->mc`.