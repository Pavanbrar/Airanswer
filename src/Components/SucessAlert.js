import React from 'react'
import { Component } from 'react'

export default class SucessAlert extends Component {
    render(){
        return (
            <div className="alert alert-success" role="alert">
                <strong>Well done!!</strong> Data added Successfully.
            </div>
        )
    }
}
