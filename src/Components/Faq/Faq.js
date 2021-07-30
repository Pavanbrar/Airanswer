
import React, { Component } from 'react'
import Header from '../../Header'
import Sidebar from '../Sidebar';
import { Link } from 'react-router-dom';
//import axios from 'axios';
//import Cookie from 'universal-cookie';
import apiClient from "../Service/api";
class Faq extends Component {

    constructor(props) {
        super(props)
        this.state = {
          users: []
        };
      }
    componentDidUpdate(){
        this.refreshList();
    }
    componentDidMount(){
        this.refreshList();
    }
    refreshList() {
        const token = JSON.parse(localStorage.getItem('ACCESS_TOKEN'));
        apiClient.get('/api/faq-all', {
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${token}`
            }
        }).then(response => {
            try {
                if (response.data.code === 200) {
                    this.setState({
                        users: response.data.data
                      })
                }
                    
            } catch (err) {
                console.log(err.message);
            }
        
        });
        
    }
    deleteFaq = (e, id) => {
        const Swal = require('sweetalert2');
        if(window.confirm("Are you sure.!!")){
            const token = JSON.parse(localStorage.getItem('ACCESS_TOKEN'));
            apiClient.delete('/api/faq-delete/'+id, {
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${token}`
                }
            }).then(response => {
                try {
                    if (response.data.code === 200) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.data.message,
                            icon: "success",
                            button: "OK"
                        })
                    
                    }
                        
                } catch (err) {
                    console.log(err.message);
                }
            
            });
        }   
    }
    render() {
        var user_Table = "";
        if (this.state.loading) {
            user_Table = <tr><td colSpan="9"><h4>Loading....</h4></td></tr>
        } else {
            user_Table = this.state.users.map((item) => {
                return (
                    <tr key={item.id}>
                        <td>{item.id}</td>
                        <td>{item.question}</td>
                        <td>{item.answer}</td>
                        <td>
                            <Link to={'edit-faq/'+item.id} className="btn btn-success btn-sm">Edit</Link>
                            <button type="button" onClick={(e) => this.deleteFaq(e, item.id)} className="btn btn-danger btn-sm">Delete </button>
                        </td>
                    </tr>
                )
                });
        }
        return (
            <>
                <Header />
                <div className="row">
                    <Sidebar />
                    <div className="col-md-9">
                        <div className="container">
                            <div className="row">
                                <div className="col-md-12">
                                    <div className="card">
                                        <div className="card-header">
                                            <h3 className="mt-2">FAQ Data
                                                <Link to={'add-faq'} className="btn btn-primary btn-sm float-right ">Add Faq</Link>
                                            </h3>
                                        </div>
                                        <div className="card-body">
                                            <table className="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Question</th>
                                                        <th>Answer</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {user_Table}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </>
        )
    }
}
export default Faq
