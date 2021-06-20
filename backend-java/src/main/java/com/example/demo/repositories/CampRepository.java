package com.example.demo.repositories;

import com.example.demo.entities.Camp;
import com.example.demo.entities.User;
import org.springframework.data.repository.PagingAndSortingRepository;

import java.util.Optional;

public interface CampRepository extends PagingAndSortingRepository<Camp, String> {
    @SuppressWarnings("unused")
    Optional<Camp> findByUserUsername(String s);
}
