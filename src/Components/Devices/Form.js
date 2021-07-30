import React,{useState} from 'react'
import RegisterDevice from './RegisterDevice';
import FormSuccess from '../Service/FormSuccess';
const Form = () => {
    const [isSubmitted, setIsSubmitted]= useState(false);

    function submitForm(){
        setIsSubmitted(true);
        
    }
    return (
        <div>
          {!isSubmitted ? (
          <RegisterDevice submitForm={submitForm} />
        ) : (
          <FormSuccess />
        )};
           
        </div> 
    );
};

export default Form;
