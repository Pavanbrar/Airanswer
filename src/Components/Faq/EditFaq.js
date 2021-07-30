import Header from '../../Header'
import Sidebar from '../Sidebar';
import React from 'react';
import { Link } from 'react-router-dom';
import { withRouter } from 'react-router-dom';
import apiClient from "../Service/api";
import ValidateInfo from "../Service/ValidateInfo";
import axios from 'axios';
import SucessAlert from '../SucessAlert';
import ErrorAlert from '../ErrorAlert';
import { useState, useEffect } from 'react';
import Swal from 'sweetalert2';
import { Redirect } from 'react-router';

class EditFaq extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            question: '',
            answer: '',
            redirect: false,
            alert_message: '',
            error_list: [],
        }
        this.onChangeQuestion = this.onChangeQuestion.bind(this);
        this.onChangeAnswer = this.onChangeAnswer.bind(this);
        this.onSubmit = this.onSubmit.bind(this);
    };

    updateFaqData = async (e) => {
        const Swal = require('sweetalert2');
        const token = JSON.parse(localStorage.getItem('ACCESS_TOKEN'));
        e.preventDefault();
        const res = apiClient.put('http://localhost:8000/api/faq-update/' + this.props.match.params.id, this.state, {
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${token}`
            }
        }).then(res => {
            if (res.data.code === 200) {
                Swal.fire({
                    title: 'Updated!',
                    text: res.data.message,
                    icon: "success",
                    button: "OK"
                })
            } else {
                this.setState({
                    error_list: res.data.message,
                });
            }
        }).catch(error => {
            Swal.fire({
                title: 'Ops!',
                text: res.data.message,
                icon: "error",
                button: "OK"
            })
        });
    }
    componentDidMount() {
        const token = JSON.parse(localStorage.getItem('ACCESS_TOKEN'));
        console.log(this.props.match.params.id);
        axios.get('http://localhost:8000/api/get-faq-id/'+this.props.match.params.id, {
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${token}`
            }
        })
            .then(response => {
                try {
                    if (response.data.code === 200) {
                        this.setState({
                            question: response.data.data[0].question,
                            answer: response.data.data[0].answer
                        });
                    }
                } catch (err) {
                    console.log(err.message);
                }
                
            })
            .catch(function (error) {
                console.log(error);
            });
    }
    onChangeQuestion(e) {
        this.setState({
            question: e.target.value
        })
    }
    onChangeAnswer(e) {
        this.setState({
            answer: e.target.value
        })
    }
    
    onSubmit(e) {
        e.preventDefault();
        const obj = {
            question: this.state.question,
            answer: this.state.answer
        };
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
                                            <h3 className="mt-2">FAQ Update
                                                <Link to={'faq'} className="btn btn-primary btn-sm float-right ">Back </Link>
                                            </h3>
                                        </div>
                                        <div className="card-body">
                                            <div className="col-sm-9 app-container">
                                                <form onSubmit={this.updateFaqData} >
                                                    <div className="form-group mb-3">
                                                        <label>Question</label>
                                                        <input type="text" name="question" defaultValue={this.state.question} onChange={this.onChangeQuestion} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.question}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Answer</label>
                                                        <input type="text" name="answer" defaultValue={this.state.answer} onChange={this.onChangeAnswer} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.answer}</span>
                                                    </div>
                                                    
                                                    <div className="form-group mb-3">
                                                        <button type="submit" className="btn btn-primary">Update FAQ</button>
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
        );
    }
}
export default withRouter(EditFaq)
