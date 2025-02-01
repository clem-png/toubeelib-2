# API - Toubeelib

## Membres du groupe :
- [Etique Kevin](https://github.com/EtiqueKevin)
- [Netange Clément](https://github.com/clem-png)
- [Quilliec Amaury](https://github.com/Aliec-AQ)
- [Ringot Mathias](https://github.com/4n0m4lie)

## Données de population :
- [Praticiens](https://github.com/clem-png/toubeelib-2/tree/main/api-praticiens/app/sql)
- [RDV](https://github.com/clem-png/toubeelib-2/tree/main/api-rdv/app/sql)
- [Auth](https://github.com/clem-png/toubeelib-2/tree/main/api-auth/app/sql)
- [patient](https://github.com/clem-png/toubeelib-2/tree/main/api-patient/app/src)

## Données utiles pour tester :
- Connection en tant qu'Admin :
  - email : admin@test.fr
  - password : test
- Connection en tant que Praticien :
  - email : praticien@test.fr
  - password : test
- Connection en tant que Patient :
  - email : patient@test.fr
  - password : test

## Routes :

### Praticiens :
> GET /praticiens
>
>> Récupère la liste des praticiens
>>
>> Paramètres : Aucun

> GET /praticiens/{id}
>
>> Récupère un praticien par son ID
>>
>> Paramètres : id (uuid)

> GET /specialites/{id}
>
>> Récupère un praticien par spécialité ID
>>
>> Paramètres : id (uuid)

> POST /praticiens/{id}/disponibilites
>
>> Récupère les disponibilités d'un praticien
>>
>> Paramètres : id (uuid)
>>
>> Body :

| nom attribut | type   | description                              |
|--------------|--------|------------------------------------------|
| dateDeb      | string | Date de début de la période (y-m-d H:i)  |
| dateFin      | string | Date de fin de la période (y-m-d H:i)    |

> POST /praticiens/{id}/planning
>
>> Récupère le planning d'un praticien
>>
>> Paramètres : id (uuid)
>>
>> Body :

| nom attribut | type   | description                              |
|--------------|--------|------------------------------------------|
| dateDeb      | string | Date de début de la période (y-m-d H:i)  |
| dateFin      | string | Date de fin de la période (y-m-d H:i)    |
| idSpe        | uuid   | ID de la spécialité                      |
| type         | string | Type de rendez-vous (presentiel ou téléconsultation) |

> POST /praticiens/{id}/indisponibilite
>
>> Ajoute une indisponibilité pour un praticien
>>
>> Paramètres : id (uuid)
>> 
>> Body :

| nom attribut | type   | description                              |
|--------------|--------|------------------------------------------|
| dateDeb      | string | Date de début de la période (y-m-d H:i)  |
| dateFin      | string | Date de fin de la période (y-m-d H:i)    |

### Rendez-vous :
> PUT /rdvs/{id}/annuler
>
>> Annule un rendez-vous
>>
>> Paramètres : id (uuid)

> GET /rdvs/{id}
>
>> Récupère un rendez-vous
>>
>> Paramètres : id (uuid)

> POST /rdvs
>
>> Crée un rendez-vous
>>
>> Body :

| nom attribut | type   | description                                          |
|--------------|--------|------------------------------------------------------|
| idPatient    | uuid   | ID du patient                                        |
| idPraticien  | uuid   | ID du praticien                                      |
| date         | string | Date du rendez-vous (y-m-d hh:mm)                    |
| specialite   | uuid   | ID de la spécialité (optionnel)                      |
| type         | string | Type de rendez-vous (présentiel ou téléconsultation) |

> PATCH /rdvs/{id}
> 
> Modifie un rendez-vous
> 
> Paramètres : id (uuid)
> 
> Body :

| nom attribut | type   | description                                                             |
|--------------|--------|-------------------------------------------------------------------------|
| idPatient    | uuid   | ID du patient  (optionnel, si vous voulez changer le patient du rdv)     |
| specialite   | uuid   | ID de la spécialité (optionnel, si vous voulez changer la spécialité)    |

> PUT /rdvs/{id}/payer
> 
> Payer un rendez-vous
> 
> Paramètres : id (uuid)

> PUT /rdvs/{id}/honorer
> 
> Mettre le statut du rendez-vous à honoré
> 
> Paramètres : id (uuid)

> PUT /rdvs/{id}/non-honorer
> 
> Mettre le statut du rendez-vous à non honoré
> 
> Paramètres : id (uuid)

### Authentification :
> POST /users/signin
>
>> Authentifie un utilisateur
>>
>> En-tête de la requête (Header) :

| nom attribut | type   | description                   |
|--------------|--------|-------------------------------|
| Authorization | string | En-tête contenant les identifiants encodés en Base64 dans le format Basic <base64(email:password) |

> POST /users/register
>
>> Enregistre un nouvel utilisateur
>>
>> Body :

| nom attribut | type   | description                   |
|--------------|--------|-------------------------------|
| Username     | string | Email de l'utilisateur        |
| Password     | string | Mot de passe de l'utilisateur |

> POST /users/refresh
>
>> Rafraîchit le token d'authentification
>>
>> Paramètres : Aucun

### Patients :
> POST /patient
>
>> Crée un nouveau patient
>>
>> Body :

| nom attribut  | type   | description                |
|---------------|--------|----------------------------|
| nom           | string | Nom du patient             |
| prenom        | string | Prénom du patient          |
| dateNaissance | string | Date de naissance (y-m-d)  |
| adresse       | string | Adresse du patient         |
| telephone     | string | Numéro de téléphone        |
| mail          | string | Email du patient           |
| password      | string | Mot de passe du patient    |

> GET /patient/{id}
>
>> Récupère un patient par ID
>>
>> Paramètres : id (uuid)
