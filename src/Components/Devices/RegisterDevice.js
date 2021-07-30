import { ErrorSharp } from '@material-ui/icons';
import React from 'react';
import Header from '../../Header'
import Sidebar from '../Sidebar';
import { Link } from 'react-router-dom';
import useForm from '../Service/useForm';
import validate from '../Service/ValidateInfo';
const RegisterDevice = (submitForm) => {
    const {handleChange,handleSubmit,values,errors} = useForm(submitForm,validate);
    
    return (
        <div>
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
                                                <form onSubmit={handleSubmit}>
                                                    <div className="form-group mb-3">
                                                        <label htmlFor="username">Username</label>
                                                        <input type="text" 
                                                        name="username" 
                                                        onChange={handleChange} 
                                                        value={values.username}
                                                        className="form-control" />
                                                        {errors.username && <p>{errors.username}</p>}
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>First Name</label>
                                                        <input type="text"
                                                         name="firstname" 
                                                         value={values.firstname} 
                                                         onChange={handleChange}
                                                         className="form-control" />
                                                         {errors.firstname && <p>{errors.firstname}</p>}
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Last Name</label>
                                                        <input type="text"
                                                        name="lastname" 
                                                        value={values.lastname} 
                                                        onChange={handleChange} 
                                                        className="form-control" />
                                                        {errors.lastname && <p>{errors.lastname}</p>}
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Email</label>
                                                        <input type="text" 
                                                        name="email" 
                                                        value={values.email} 
                                                        onChange={handleChange}
                                                        className="form-control" />
                                                        {errors.email && <p>{errors.email}</p>}
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>DOB</label>
                                                        <input type="text" 
                                                        name="dob" 
                                                        value={values.dob} 
                                                        onChange={handleChange} 
                                                        className="form-control" />
                                                    </div>
                                                    <div className="form-group mb-3">
                                                        <label>Campany name</label>
                                                        <input type="text" 
                                                        name="company_name" 
                                                        value={values.company_name} 
                                                        onChange={handleChange} 
                                                        className="form-control" />
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

        </div>
    )
}

export default RegisterDevice
