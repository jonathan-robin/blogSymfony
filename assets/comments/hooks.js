import React, {useState} from 'react'

export function usePaginatedFect(url){ 
    const [loading, setLoading] = useState(null)
    const [items, setItems] = useState([])

    const load = useCallback(async() => {
        setLoading(true)
        const response = await fetch(url)

        setLoading(false)
    },[url])
}
