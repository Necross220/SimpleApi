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
        //POSTMAN CRAZINESS WITH NUMBER
        "phones[0]": 8090000001,
        "phones[1]": 8090000002,
        "phones[2]": 8090000003
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