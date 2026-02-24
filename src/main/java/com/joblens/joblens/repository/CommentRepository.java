package com.joblens.joblens.repository;

import com.joblens.joblens.entity.Comment;
import org.springframework.data.repository.CrudRepository;

public interface CommentRepository extends CrudRepository<Comment, Long> {
}
