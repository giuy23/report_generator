import { IForm } from '@/Interfaces/FormInterfaces';
import axios from 'axios';

export const fetchData = async (payload: IForm) => {
    return axios.post(route('report.generate'), {
        start_date: payload.startDate,
        end_date: payload.endDate
    });
};
