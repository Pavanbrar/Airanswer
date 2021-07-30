import React, { Component } from 'react'
import Header from '../../Header'
import Sidebar from '../Sidebar';
import { Link } from 'react-router-dom';
import axios from 'axios';
import Swal from 'sweetalert2';
class AddDevice extends Component {

    state = {
        firstname: '',
        lastname: '',
        username: '',
        dob: '',
        email: '',
        company_name: '',
        password:'',
        phone :'',
        gender:'',
        error_list:[],
    }
    handleInput = (e) => {
        this.setState({
            [e.target.name]: e.target.value
        })
    }    
    saveDevice = async (e) => {
        const Swal = require('sweetalert2');
        e.preventDefault();
        const res = await axios.post('http://localhost:8000/api/register', this.state, {
            xsrfHeaderName: "X-XSRF-TOKEN", // change the name of the header to "X-XSRF-TOKEN" and it should works
            withCredentials: true
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
                firstname: '',
                lastname: '',
                username: '',
                dob: '',
                email: '',
                password:'',
                gender :'',
                phone :'',
                company_name: '',
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
                                            <h3 className="mt-2">Add Device
                                                <Link to={'device'} className="btn btn-primary btn-sm float-right ">Back</Link>
                                            </h3>
                                            <div className="col-sm-9 app-container">
                                                <form onSubmit={this.saveDevice}>
                                                    <div className="form-group mb-3">
                                                        <label>Username</label>
                                                        <input type="text" name="username" onChange={this.handleInput} value={this.state.username} className="form-control" />
                                                    <span className="text-danger">{this.state.error_list.username}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>First Name</label>
                                                        <input type="text" name="firstname" value={this.state.firstname} onChange={this.handleInput} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.firstname}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Last Name</label>
                                                        <input type="text" name="lastname" value={this.state.lastname} onChange={this.handleInput} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.lastname}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Email</label>
                                                        <input type="text" name="email" value={this.state.email} onChange={this.handleInput} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.email}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>DOB</label>
                                                        <input type="text" name="dob" value={this.state.dob} onChange={this.handleInput} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.dob}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Gender</label>
                                                        <input type="text" name="gender" value={this.state.gender} onChange={this.handleInput} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.gender}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Phone</label>
                                                        <input type="text" name="phone" value={this.state.phone} onChange={this.handleInput} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.phone}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Password</label>
                                                        <input type="password" name="password" value={this.state.password} onChange={this.handleInput} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.password}</span>
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Campany name</label>
                                                        <input type="text" name="company_name" value={this.state.company_name} onChange={this.handleInput} className="form-control" />
                                                        <span className="text-danger">{this.state.error_list.company_name}</span>
                                                     </div>
                                                    <div className="form-group mb-3">
                                                        <button type="submit" className="btn btn-primary">Save Device</button>
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
export default AddDevice
