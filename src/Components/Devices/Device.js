import React, { Component } from 'react'
import Header from '../../Header'
import Sidebar from '../Sidebar';
import { Link } from 'react-router-dom';
//import axios from 'axios';
//import Cookie from 'universal-cookie';
import apiClient from "../Service/api";
class Device extends Component {

    constructor(props) {
        super(props)
        this.state = {
            users: []
        };
    }

    //  export const sendLogin = (data) => {

    //  }
    // authAxios = axios.create({
    //     baseURL : apiUrl,
    //     headers : `Bearer ${accessToken}`
    // })
    //token = document.head.querySelector('meta[name="csrf-token"]');

    componentDidMount() {
        //   if (! this.state.users) {
        // var cookie = new Cookie();
        const token = JSON.parse(localStorage.getItem('ACCESS_TOKEN'));
        apiClient.get('/api/get_user', {
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
    deleteDevice = (e, id) => {
        // const res = axios.delete('http://llocalhost:8000/api/')
    }
    render() {
        var user_Table = "";
        if (this.state.loading) {
             user_Table = <tr><td colSpan="9"><h4>Loading....</h4></td></tr>
        } else {
             user_Table = this.state.users.map((item) => {
                  return (
                <tr key={item.user_id}>
                    <td>{item.user_id}</td>
                    <td>{item.firstname}</td>
                    <td>{item.lastname}</td>
                    <td>{item.email}</td>
                    <td>{item.phone}</td>
                    <td>{item.dob}</td>
                    <td>{item.gender}</td>
                    <td>{item.company_name}</td>
                    <td>
                        <Link to={'edit-device/' + item.user_id} className="btn btn-success btn-sm">Edit</Link>
                        <button type="button" onClick={(e) => this.deleteDevice(e, item.user_id)} className="btn btn-danger btn-sm">Delete </button>
                    </td>
                </tr>
                //  )
            )});
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
                                            <h3 className="mt-2">Device Data
                                                <Link to={'add-device'} className="btn btn-primary btn-sm float-right ">Add Device</Link>
                                            </h3>
                                        </div>
                                        <div className="card-body">
                                            <table className="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>First Name</th>
                                                        <th>Last Name</th>
                                                        <th>Email</th>
                                                        <th>Phone number</th>
                                                        <th>DOB</th>
                                                        <th>Gender</th>
                                                        <th>Company Name</th>
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
export default Device
