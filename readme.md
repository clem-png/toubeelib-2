# API - Toubeelib

## Membres du groupe :
- Etique Kevin
- Netange Clément
- Quilliec Amaury
- Ringot Mathias

## Routes :

### Rendez-vous :
>PUT /rdvs/:id/annuler
>
>>Annule un rendez-vous
>>
>>Paramètres : id (uuid)


>GET /rdvs/:id
>
>>Récupère un rendez-vous
>>
>>Paramètres : id (uuid)


>POST /rdvs
>
>>Crée un rendez-vous
>>
>>Body :

| nom attribut | type   | description                                          |
|--------------|--------|------------------------------------------------------|
| idPatient    | uuid   | id du patient                                        |
| idPraticien  | uuid   | id du praticien                                      |
| date         | string | date du rendez-vous (y-m-d hh:mm)                    |
| specialite   | uuid   | id de la specialite (optionnel)                      |
| type         | string | type de rendez-vous (presentiel ou teleconsultation) |

> PATCH /rdvs/:id
> 
> > Modifie un rendez-vous
> >
> > Paramètres : id (uuid)
> >
> > Body :

| nom attribut | type   | description                                                             |
|--------------|--------|-------------------------------------------------------------------------|
| idPatient    | uuid   | id du patient  ( optionnel, si envie de changer le patient du rdv )     |
| specialite   | uuid   | id du praticien ( optionnel, si envie de changer la specialite du rdv ) |

> PUT /rdvs/:id/payer
> 
> > Payer un rendez-vous
> >
> > Récupérer les liens de paiement
> >
> > Paramètres : id (uuid)

> PUT /rdvs/:id/honorer
> 
> > Mettre le status du rendez-vous à honoré
> >
> > Paramètres : id (uuid)

### Praticiens :
> GET /praticiens
>
>> Récupère la liste des praticiens
>>
>> Paramètres : Aucun

> GET /praticiens/{ID-PRATICIEN}/disponibilites
>
>> Récupère les disponibilités d'un praticien
>>
>> Paramètres : ID-PRATICIEN (uuid)
>>
>> Body :

| nom attribut | type   | description                              |
|--------------|--------|------------------------------------------|
| dateDeb      | string | Date de début de la période (y-m-d H:i)  |
| dateFin      | string | Date de fin de la période (y-m-d H:i)    |

> GET /praticiens/{ID-PRATICIEN}/planning
>
>> Récupère le planning d'un praticien
>>
>> Paramètres : ID-PRATICIEN (uuid)
>>
>> Body :

| nom attribut | type   | description                              |
|--------------|--------|------------------------------------------|
| dateDeb      | string | Date de début de la période (y-m-d H:i)  |
| dateFin      | string | Date de fin de la période (y-m-d H:i)    |
| idSpe        | uuid   | ID de la spécialité                      |
| type         | string | Type de rendez-vous                      |


> GET /praticiens/{ID-PRATICIEN}
>
>> Récupère les informations d'un praticien par ID
>>
>> Paramètres : ID-PRATICIEN (uuid)

> POST /praticiens
>
>> Ajoute un nouveau praticien
>>
>> Body :

| nom attribut | type   | description                |
|--------------|--------|----------------------------|
| nom          | string | Nom du praticien           |
| prenom       | string | Prénom du praticien        |
| telephone    | string | Numéro de téléphone        |
| adresse      | string | Adresse du praticien       |
| specialite   | uuid   | ID de la spécialité        |

### Patients :
> POST /patients
>
>> Ajoute un nouveau patient
>>
>> Body :

| nom attribut  | type   | description                |
|---------------|--------|----------------------------|
| nom           | string | Nom du patient             |
| prenom        | string | Prénom du patient          |
| adresse       | string | Adresse du patient         |
| telephone     | string | Numéro de téléphone        |
| mail          | string | Email du patient           |
| dateNaissance | string | Date de naissance (y-m-d)  |
| password      | string | Mot de passe du patient    |

### Authentification :

> POST /users/signin
>
>> Authentifie un utilisateur
>>
>> Autorization : Basic Auth

| nom attribut | type   | description                   |
|--------------|--------|-------------------------------|
| Username     | string | Email de l'utilisateur        |
| Password     | string | Mot de passe de l'utilisateur |