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
import { useState, useEffect} from 'react';
import Swal from 'sweetalert2';
import { Redirect } from 'react-router';

class UpdateDevice extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            firstname: '',
            lastname: '',
            email: '',
            dob: '',
            username: '',
            gender: '',
            password: '',
            phone: '',
            company_name: '',
            redirect: false,
            alert_message: '',
            error_list: [],
        }
        this.onChangeFirstName = this.onChangeFirstName.bind(this);
        this.onChangeLastName = this.onChangeLastName.bind(this);
        this.onChangeEmail = this.onChangeEmail.bind(this);
        this.onChangeDob = this.onChangeDob.bind(this);
        this.onChangeUserName = this.onChangeUserName.bind(this);
        this.onChangeGender = this.onChangeGender.bind(this);
        this.onChangeCompanyname = this.onChangeCompanyName.bind(this);
        this.onChangePassword = this.onChangePassword.bind(this);
        this.onChangePhone = this.onChangePhone.bind(this);
        this.onSubmit = this.onSubmit.bind(this);
    };

    updateDeviceData = async (e) => {
        const Swal = require('sweetalert2');
        document.getElementById("updateBtn").innerHTML = 'Updating...';
        const token = JSON.parse(localStorage.getItem('ACCESS_TOKEN'));
        e.preventDefault();
        const res = apiClient.put('http://localhost:8000/api/update/' + this.props.match.params.id, this.state, {
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
        axios.get('http://localhost:8000/api/getuserbyid/' + this.props.match.params.id, {
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${token}`
            }
        })
            .then(response => {
                this.setState({
                    firstname: response.data.data.firstname,
                    lastname: response.data.data.lastname,
                    email: response.data.data.email,
                    dob: response.data.data.dob,
                    gender: response.data.data.gender,
                    username: response.data.data.username,
                    phone: response.data.data.phone,
                    password: response.data.data.password,
                    company_name: response.data.data.company_name,
                });
            })
            .catch(function (error) {
                console.log(error);
            });
    }
    onChangeFirstName(e) {
        this.setState({
            firstname: e.target.value
        })
    }
    onChangeLastName(e) {
        this.setState({
            lastname: e.target.value
        })
    }
    onChangeEmail(e) {
        this.setState({
            email: e.target.value
        })
    }
    onChangeUserName(e) {
        this.setState({
            username: e.target.value
        })
    }
    onChangeDob(e) {
        this.setState({
            dob: e.target.value
        })
    }
    onChangeCompanyName(e) {
        this.setState({
            company_name: e.target.value
        })
    }
    onChangeGender(e) {
        this.setState({
            gender: e.target.value
        })
    }
    onChangePassword(e) {
        this.setState({
            password: e.target.value
        })
    }
    onChangePhone(e) {
        this.setState({
            phone: e.target.value
        })
    }

    onSubmit(e) {
        e.preventDefault();
        const obj = {
            firstname: this.state.firstname,
            lastname: this.state.lastname,
            username: this.state.username,
            email: this.state.email,
            dob: this.state.dob,
            gender: this.state.gender,
            phone: this.state.phone,
            password: this.state.password,

            company_name: this.state.company_name
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
                                            <h3 className="mt-2">Device Update
                                                <Link to={'device'} className="btn btn-primary btn-sm float-right ">Back </Link>
                                            </h3>
                                        </div>
                                        <div className="card-body">
                                            <div className="col-sm-9 app-container">
                                                <form onSubmit={this.updateDeviceData} >
                                                    <div className="form-group mb-3">
                                                        <label>Username</label>
                                                        <input type="text" name="username" defaultValue={this.state.username} onChange={this.onChangeUserName} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.username}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>First Name</label>
                                                        <input type="text" name="firstname" defaultValue={this.state.firstname} onChange={this.onChangeFirstName} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.firstname}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Last Name</label>
                                                        <input type="text" name="lastname" defaultValue={this.state.lastname} onChange={this.onChangeLastName} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.lastname}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Email</label>
                                                        <input type="text" name="email" defaultValue={this.state.email} onChange={this.onChangeEmail} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.email}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>DOB</label>
                                                        <input type="text" name="dob" defaultValue={this.state.dob} onChange={this.onChangeDob} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.dob}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Gender</label>
                                                        <input type="text" name="gender" defaultValue={this.state.gender} onChange={this.onChangeGender} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.gender}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Password</label>
                                                        <input type="text" name="password" defaultValue={this.state.password} onChange={this.onChangePassword} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.password}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Phone</label>
                                                        <input type="text" name="phone" defaultValue={this.state.phone} onChange={this.onChangePhone} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.phone}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Campany name</label>
                                                        <input type="text" name="company_name" defaultValue={this.state.company_name} onChange={this.onChangeCompanyname} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.company_name}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <button type="submit" id="updateBtn" className="btn btn-primary">Update Device</button>
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
export default withRouter(UpdateDevice)
