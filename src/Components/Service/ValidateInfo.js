import React from 'react'

export default function ValidateInfo(values) {
    let errors ={}
    
    if(!values.username.trim()){
        errors.username = "Username is required"
    }
    if(!values.username.trim()){
        errors.firstname = "Firstname is required"
    }
    if(!values.username.trim()){
        errors.lastname = "Lastname is required"
    }
    if (!/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[A-Za-z]+$/.test(values.email)) { 
        errors.email = "Please enter valid email"
    }
    return errors;
}

