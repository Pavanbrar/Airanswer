import React, { Component } from 'react'
import Header from '../../Header'
import Sidebar from '../Sidebar';
import { Link } from 'react-router-dom';
import axios from 'axios';
import Swal from 'sweetalert2';
import apiClient from "../Service/api";
class AddFaq extends Component {

    state = {
        question: '',
        answer: '',
       
        error_list:[],
    }
    handleInput = (e) => {
        this.setState({
            [e.target.name]: e.target.value
        })
    }    
    saveFaq = async (e) => {
        const Swal = require('sweetalert2');
        const token = JSON.parse(localStorage.getItem('ACCESS_TOKEN'));
        e.preventDefault();
        const res = await apiClient.post('/api/faq-add', this.state, {
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${token}`
            }
        });
        if (res.data.code === 200) {
            console.log(res.data.message);
              Swal.fire({
                        title: 'Added!',
                        text: res.data.message,
                        icon: "success",
                        button: "OK"
                    })
            this.setState({
                question: '',
                answer: '',
                
            });
        }else{
            console.log(res.data.message);
            this.setState({
                error_list :res.data.message,
            });
        }
    }
    render() {
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
                                            <h3 className="mt-2">Add Faq
                                                <Link to={'faq'} className="btn btn-primary btn-sm float-right ">Back</Link>
                                            </h3>
                                            <div className="col-sm-9 app-container">
                                                <form onSubmit={this.saveFaq}>
                                                    <div className="form-group mb-3">
                                                        <label>Question</label>
                                                        <input type="text" name="question" onChange={this.handleInput} value={this.state.question} className="form-control" />
                                                    <span className="text-danger">{this.state.error_list.question}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Answer</label>
                                                        <input type="text" name="answer" value={this.state.answer} onChange={this.handleInput} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.answer}</span>
                                                    </div>
                                                   
                                                    <div className="form-group mb-3">
                                                        <button type="submit" className="btn btn-primary">Save FAQ</button>
                                                    </div>
                                                </form>
                                            </div>
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
export default AddFaq
