import React from 'react'
import { Component } from 'react'

export default class  ErrorAlert extends Component {
    render(){
    return (
        <div>
             <div className="alert alert-danger" role="alert">
                <strong>Warning!!</strong> Data not added Successfully.
            </div>
        </div>
    )
    }
}
