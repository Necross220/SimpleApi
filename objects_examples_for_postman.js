//READ
let data = {
        //http://localhost/Contacts/read -> TODOS
        id: 1 // INDIVIDUAL
}

//CREATE
let data = {
        name: 'David',
        lastname: 'Uri',
        email: 'UPDATED@EMAIL.com',
        phones: ["809000001", "809000002", "809000003"]
}

//UPDATE
let data = {
        id: 1,
        name: 'UPDARTED NAME',
        lastname: 'UPDATED LASTNAME',
        email: 'UPDATED@EMAIL.com'
}

//DELETE
let data = {
        //Is an update changing the status cause we are not suppose to delete stuff from the database
        id: 1
}