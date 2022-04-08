//READ
{
        //http://localhost/Contacts/read -> TODOS
        id: 1 // INDIVIDUAL
}

//CREATE
{
        name: 'David',
        lastname: 'Uri',
        email: 'UPDATED@EMAIL.com'
}

//UPDATE
{
        id: 1,
        name: 'UPDARTED NAME',
        lastname: 'UPDATED LASTNAME',
        email: 'UPDATED@EMAIL.com'
}

//DELETE
{
        //Is an update changing the status cause we are not suppose to delete stuff from the database
        id: 1
}