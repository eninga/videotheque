# Vidéothèque en ligne

## Installation

* Cloner ce depot

`git clone https://github.com/eninga/videotheque.git`

* Lancer `composer install`

* Pour recevoir les emails lors des ajouts ou suppressions des films dans `app/config/parameters.yml` renseigner l'adresse email de l'admin

```
parameters:
       email_admin: #email de l'admin ici

```
:heavy_exclamation_mark: Le service `video.notify` utilise le paramètre `email_admin`.
Il y aura une erreur si `email_admin` n'est pas trouvé.

  
