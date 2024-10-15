# API - Toubeelib

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

