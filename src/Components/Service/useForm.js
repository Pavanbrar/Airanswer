import {useState, useEffect} from 'react';
//import Validate from '../Service/ValidateInfo';
import axios from 'axios';
const useForm = (callback, validate) => {
    const [values,setValues] = useState({
        firstname: '',
        lastname: '',
        email: '',
        dob: '',
        username: '',
        company_name: '',
    });
    const [errors,setErrors] = useState({});
    const [isSubmitting,setIsSubmitting] = useState(false);

    const handleChange = e => {
        const {name, value} = e.target;
        setValues({
            ...values,
            [name] :value
        });
    };

    const handleSubmit = e =>{
        e.preventDefault();
        setErrors(validate(values));
        setIsSubmitting(true);
       
    };

    useEffect(
        () => {
          if (Object.keys(errors).length === 0 && isSubmitting) {
            callback();
          }
        },
        [errors]
      );
      return { handleChange, handleSubmit, values, errors };
}

export default useForm
