package com.example.demo.repositories;

import com.example.demo.entities.User;
import org.springframework.data.repository.PagingAndSortingRepository;

public interface UserRepository extends PagingAndSortingRepository<User, String> {

}
